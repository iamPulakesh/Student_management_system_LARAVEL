<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
        $allCourses = $this->allowedCoursesQuery($request)->get();
        $allowedCourseIds = $allCourses->pluck('id');

        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');
        $attendanceDate = trim((string) $request->input('attendance_date', ''));
        $selectedCourse = null;

        if ($request->user()->isAdmin() && ($request->has('course_id') || $request->has('attendance_date') || $request->has('department_id'))) {
            $request->validate([
                'department_id' => ['required', 'integer', 'exists:departments,id'],
                'course_id' => ['required', 'integer', 'exists:courses,id'],
                'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            ], [], [
                'department_id' => 'department',
                'course_id' => 'course',
                'attendance_date' => 'date',
            ]);
        }

        $courses = $allCourses
            ->when($departmentId, fn ($c) => $c->where('department_id', (int) $departmentId))
            ->values();

        if ($courseId !== null && $courseId !== '') {
            $selectedCourse = Course::with('department')->findOrFail((int) $courseId);
            $this->authorizeCourseAccess($request, $selectedCourse);
        }

        $studentSummaries = collect();
        $canLoadAttendance = $selectedCourse !== null && (! $request->user()->isAdmin() || $attendanceDate !== '');

        if ($canLoadAttendance) {
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

            if ($attendanceDate !== '') {
                $attendancesByStudent = Attendance::with('recordedBy')
                    ->where('course_id', $selectedCourse->id)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->get()
                    ->keyBy('student_id');
            } else {
                $attendancesByStudent = Attendance::with('recordedBy')
                    ->where('course_id', $selectedCourse->id)
                    ->orderByDesc('attendance_date')
                    ->orderByDesc('id')
                    ->get()
                    ->unique('student_id')
                    ->keyBy('student_id');
            }

            $studentSummaries = $students->map(function (Student $student) use ($attendancesByStudent) {
                $total = (int) $student->total_attendance_count;
                $present = (int) $student->present_attendance_count;
                $attendance = $attendancesByStudent->get($student->id);

                return [
                    'student' => $student,
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : null,
                    'attendance' => $attendance,
                ];
            });
        }

        $departments = $request->user()->isAdmin()
            ? Department::query()->orderBy('name')->get()
            : Department::query()->whereIn('id', $allCourses->pluck('department_id')->unique())->orderBy('name')->get();

        $allCoursesData = $allCourses->map(fn (Course $c) => [
            'id' => $c->id,
            'code' => $c->code,
            'name' => $c->name,
            'department_id' => $c->department_id,
            'department_code' => $c->department?->code,
            'semester' => $c->semester,
        ])->values();

        return view('attendance.index', [
            'routePrefix' => $routePrefix,
            'departments' => $departments,
            'departmentId' => $departmentId,
            'courses' => $courses,
            'allCoursesData' => $allCoursesData,
            'selectedCourse' => $selectedCourse,
            'courseId' => $courseId,
            'attendanceDate' => $attendanceDate,
            'studentSummaries' => $studentSummaries,
        ]);
    }

    /**
     * Show the form for recording attendance for a course and date.
     */
    public function create(Request $request): View
    {
        if ($request->user()->isAdmin()) {
            abort(403, 'Admins cannot record attendance.');
        }

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
        if ($request->user()->isAdmin()) {
            abort(403, 'Admins cannot record attendance.');
        }

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
     * Show the form for editing an attendance record.
     */
    public function edit(Request $request, Attendance $attendance): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Only administrators can modify attendance records.');
        }

        $attendance->load(['student.department', 'course.department', 'recordedBy']);

        return view('attendance.edit', [
            'attendance' => $attendance,
            'routePrefix' => 'admin',
        ]);
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Only administrators can modify attendance records.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([Attendance::STATUS_PRESENT, Attendance::STATUS_ABSENT])],
            'attendance_date' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('attendances', 'attendance_date')
                    ->where(fn ($query) => $query->where('student_id', $attendance->student_id)->where('course_id', $attendance->course_id))
                    ->ignore($attendance->id),
            ],
        ], [
            'attendance_date.unique' => 'An attendance record already exists for this student on the specified date.',
            'status.in' => 'Attendance status must be either present or absent.',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'attendance_date' => $validated['attendance_date'],
        ]);

        return redirect()
            ->route('admin.attendance.index', [
                'course_id' => $attendance->course_id,
                'attendance_date' => $attendance->attendance_date->format('Y-m-d'),
            ])
            ->with('success', "Attendance record for {$attendance->student?->full_name} updated successfully.");
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

        $courseId = $attendance->course_id;
        $attendanceDate = $attendance->attendance_date?->format('Y-m-d');

        $attendance->delete();

        return redirect()
            ->route($this->routePrefix($request) . '.attendance.index', array_filter([
                'course_id' => $courseId,
                'attendance_date' => $attendanceDate,
            ]))
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
            abort(403, 'Faculty profile not found.');
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
