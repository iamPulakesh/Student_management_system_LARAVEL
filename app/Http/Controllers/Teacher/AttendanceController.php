<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records and per-student attendance percentages.
     */
    public function index(Request $request): View
    {
        $routePrefix = $this->routePrefix($request);
        $courses = $this->allowedCoursesQuery($request)->get();
        $allowedCourseIds = $courses->pluck('id');

        $courseId = $request->input('course_id');
        $attendanceDate = trim((string) $request->input('attendance_date', ''));
        $selectedCourse = null;

        if ($courseId !== null && $courseId !== '') {
            $selectedCourse = Course::with('department')->findOrFail((int) $courseId);
            $this->authorizeCourseAccess($request, $selectedCourse);
        }

        $query = Attendance::with(['student.department', 'course.department', 'recordedBy']);

        if ($request->user()->isTeacher()) {
            $query->whereIn('course_id', $allowedCourseIds);
        }

        if ($selectedCourse !== null) {
            $query->where('course_id', $selectedCourse->id);
        }

        if ($attendanceDate !== '') {
            $query->whereDate('attendance_date', $attendanceDate);
        }

        $attendances = $query
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $studentSummaries = collect();
        if ($selectedCourse !== null) {
            $students = Student::with('user')
                ->where('department_id', $selectedCourse->department_id)
                ->withCount([
                    'attendances as total_attendance_count' => function ($q) use ($selectedCourse) {
                        $q->where('course_id', $selectedCourse->id);
                    },
                    'attendances as present_attendance_count' => function ($q) use ($selectedCourse) {
                        $q->where('course_id', $selectedCourse->id)
                            ->where('status', Attendance::STATUS_PRESENT);
                    },
                ])
                ->orderBy('student_number')
                ->get();

            $studentSummaries = $students->map(function (Student $student) {
                $total = (int) $student->total_attendance_count;
                $present = (int) $student->present_attendance_count;

                return [
                    'student' => $student,
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : null,
                ];
            });
        }

        return view('attendance.index', [
            'routePrefix' => $routePrefix,
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'courseId' => $courseId,
            'attendanceDate' => $attendanceDate,
            'attendances' => $attendances,
            'studentSummaries' => $studentSummaries,
        ]);
    }

    /**
     * Show the form for recording attendance for a course and date.
     */
    public function create(Request $request): View
    {
        $routePrefix = $this->routePrefix($request);
        $courses = $this->allowedCoursesQuery($request)->get();

        $courseId = $request->input('course_id');
        $attendanceDate = trim((string) $request->input('attendance_date', now()->toDateString()));
        if ($attendanceDate === '') {
            $attendanceDate = now()->toDateString();
        }

        $selectedCourse = null;
        $students = collect();
        $existingAttendance = collect();

        if ($courseId !== null && $courseId !== '') {
            $selectedCourse = Course::with('department')->findOrFail((int) $courseId);
            $this->authorizeCourseAccess($request, $selectedCourse);

            $students = Student::with('user')
                ->where('department_id', $selectedCourse->department_id)
                ->orderBy('student_number')
                ->get();

            $existingAttendance = Attendance::where('course_id', $selectedCourse->id)
                ->whereDate('attendance_date', $attendanceDate)
                ->pluck('status', 'student_id');
        }

        return view('attendance.create', [
            'routePrefix' => $routePrefix,
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'attendanceDate' => $attendanceDate,
            'students' => $students,
            'existingAttendance' => $existingAttendance,
        ]);
    }

    /**
     * Store newly recorded attendance rows atomically in a transaction.
     */
    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $course = Course::findOrFail((int) $validated['course_id']);
        $this->authorizeCourseAccess($request, $course);

        DB::transaction(function () use ($validated, $course, $request) {
            foreach ($validated['statuses'] as $studentId => $status) {
                $student = is_numeric($studentId) ? Student::find((int) $studentId) : null;

                if ($student === null) {
                    throw ValidationException::withMessages([
                        'statuses' => 'One or more selected students do not exist.',
                    ]);
                }

                if ((int) $student->department_id !== (int) $course->department_id) {
                    throw ValidationException::withMessages([
                        'statuses' => "Student {$student->student_number} does not belong to the selected course's department.",
                    ]);
                }

                $alreadyRecorded = Attendance::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->whereDate('attendance_date', $validated['attendance_date'])
                    ->exists();

                if ($alreadyRecorded) {
                    throw ValidationException::withMessages([
                        'attendance_date' => "Attendance for student {$student->student_number} in {$course->code} on {$validated['attendance_date']} has already been recorded.",
                    ]);
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'attendance_date' => $validated['attendance_date'],
                    'status' => $status,
                    'recorded_by_user_id' => $request->user()->id,
                ]);
            }
        });

        $routePrefix = $this->routePrefix($request);

        return redirect()
            ->route($routePrefix . '.attendance.index', [
                'course_id' => $course->id,
                'attendance_date' => $validated['attendance_date'],
            ])
            ->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(Request $request, Attendance $attendance): RedirectResponse
    {
        $course = $attendance->course;
        if ($course === null) {
            abort(404);
        }

        $this->authorizeCourseAccess($request, $course);

        $attendance->delete();

        return redirect()
            ->route($this->routePrefix($request) . '.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Get the query builder for courses accessible to the current user.
     */
    private function allowedCoursesQuery(Request $request): Builder
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return Course::with('department')->orderBy('code');
        }

        $teacher = $user->teacher;
        if ($teacher === null) {
            abort(403, 'Teacher profile not found.');
        }

        return Course::with('department')
            ->where('teacher_id', $teacher->id)
            ->orderBy('code');
    }

    /**
     * Ensure the current user can access the specified course.
     */
    private function authorizeCourseAccess(Request $request, Course $course): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $teacher = $user->teacher;
        if ($teacher === null || (int) $course->teacher_id !== (int) $teacher->id) {
            abort(403, 'You are not authorized to access attendance for this course.');
        }
    }

    /**
     * Determine the route name prefix for the current user's role.
     */
    private function routePrefix(Request $request): string
    {
        return $request->user()->isAdmin() ? 'admin' : 'teacher';
    }
}
