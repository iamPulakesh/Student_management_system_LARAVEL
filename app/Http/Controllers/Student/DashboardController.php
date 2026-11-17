<?php

/**
 * Student portal pages: dashboard, courses, attendance and marks.
 */

namespace App\Http\Controllers\Student;

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
        $student = $this->resolveAuthorizedStudent($request);

        $courses = Course::query()
            ->forStudent($student)
            ->count();

        $totalAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->count();

        $presentAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->where('status', Attendance::STATUS_PRESENT)
            ->count();

        $attendancePercentage = $totalAttendance > 0
            ? round(($presentAttendance / $totalAttendance) * 100, 1)
            : null;

        return view('student.dashboard', [
            'student' => $student,
            'courses' => $courses,
            'attendancePercentage' => $attendancePercentage,
        ]);
    }

    public function courses(Request $request): View
    {
        $student = $this->resolveAuthorizedStudent($request);

        $courses = Course::query()
            ->with(['teacher.user', 'department'])
            ->forStudent($student)
            ->orderBy('code')
            ->get();

        return view('student.courses', compact('student', 'courses'));
    }

    public function attendance(Request $request): View
    {
        $student = $this->resolveAuthorizedStudent($request);

        $attendances = Attendance::query()
            ->with('course')
            ->where('student_id', $student->id)
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(20);

        return view('student.attendance', compact('attendances'));
    }

    public function assessments(Request $request): View
    {
        $student = $this->resolveAuthorizedStudent($request);

        $assessments = Assessment::query()
            ->with('course')
            ->where('student_id', $student->id)
            ->latest('assessment_date')
            ->latest('id')
            ->paginate(20);

        return view('student.assessments', compact('assessments'));
    }

    private function resolveAuthorizedStudent(Request $request): Student
    {
        return $request->user()->student()->with(['department', 'user'])->firstOrFail();
    }
}
