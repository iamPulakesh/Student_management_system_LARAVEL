<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher()->with('department')->firstOrFail();

        $assignedCourses = Course::query()
            ->with('department')
            ->where('teacher_id', $teacher->id)
            ->addSelect([
                'eligible_students_count' => Student::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('students.department_id', 'courses.department_id'),
            ])
            ->orderBy('code')
            ->get();

        $courseIds = $assignedCourses->pluck('id');

        $totalAttendanceRecorded = $courseIds->isNotEmpty()
            ? Attendance::query()->whereIn('course_id', $courseIds)->count()
            : 0;

        $totalAssessmentsRecorded = $courseIds->isNotEmpty()
            ? Assessment::query()->whereIn('course_id', $courseIds)->count()
            : 0;

        $recentAttendances = $courseIds->isNotEmpty()
            ? Attendance::query()
                ->with(['student', 'course'])
                ->whereIn('course_id', $courseIds)
                ->latest('attendance_date')
                ->latest('id')
                ->take(5)
                ->get()
            : collect();

        $recentAssessments = $courseIds->isNotEmpty()
            ? Assessment::query()
                ->with(['student', 'course'])
                ->whereIn('course_id', $courseIds)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'assignedCourses' => $assignedCourses,
            'totalAttendanceRecorded' => $totalAttendanceRecorded,
            'totalAssessmentsRecorded' => $totalAssessmentsRecorded,
            'recentAttendances' => $recentAttendances,
            'recentAssessments' => $recentAssessments,
        ]);
    }

    public function courses(Request $request): View
    {
        $teacher = $request->user()->teacher()->with('department')->firstOrFail();

        $courses = Course::query()
            ->with(['department', 'teacher.user'])
            ->where('teacher_id', $teacher->id)
            ->addSelect([
                'eligible_students_count' => Student::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('students.department_id', 'courses.department_id'),
            ])
            ->orderBy('code')
            ->paginate(10);

        return view('courses.index', [
            'courses' => $courses,
            'isTeacherView' => true,
        ]);
    }

    public function showCourse(Request $request, Course $course): View
    {
        $teacher = $request->user()->teacher()->firstOrFail();

        if ($course->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to view this course.');
        }

        $course->load(['department', 'teacher.user']);

        $students = Student::query()
            ->with('user')
            ->where('department_id', $course->department_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('courses.show', [
            'course' => $course,
            'students' => $students,
            'isTeacherView' => true,
        ]);
    }
}
