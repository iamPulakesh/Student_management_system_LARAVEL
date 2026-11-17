<?php

/**
 * Faculty dashboard and course pages.
 */

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
        $teacher = $request->user()->teacher()->firstOrFail();

        $courseIds = Course::query()->where('teacher_id', $teacher->id)->pluck('id');

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'assignedCoursesCount' => $courseIds->count(),
            'totalAttendanceRecorded' => Attendance::query()->whereIn('course_id', $courseIds)->count(),
            'totalAssessmentsRecorded' => Assessment::query()->whereIn('course_id', $courseIds)->count(),
        ]);
    }

    public function courses(Request $request): View
    {
        $teacher = $request->user()->teacher()->with('department')->firstOrFail();

        $courses = Course::query()
            ->with(['department', 'teacher.user'])
            ->where('teacher_id', $teacher->id)
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

        $course->load('department');

        $students = Student::query()
            ->with('user')
            ->enrolledIn($course)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('courses.show', [
            'course' => $course,
            'students' => $students,
        ]);
    }
}
