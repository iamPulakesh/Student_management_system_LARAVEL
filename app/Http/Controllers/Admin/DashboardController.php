<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = Student::query()->count();
        $totalTeachers = Teacher::query()->count();
        $totalDepartments = Department::query()->count();
        $totalCourses = Course::query()->count();

        $totalAttendanceRecords = Attendance::query()->count();
        $presentAttendanceRecords = Attendance::query()
            ->where('status', Attendance::STATUS_PRESENT)
            ->count();

        $overallAttendancePercentage = $totalAttendanceRecords > 0
            ? round(($presentAttendanceRecords / $totalAttendanceRecords) * 100, 1)
            : null;

        $recentStudents = Student::query()
            ->with(['department', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $recentAssessments = Assessment::query()
            ->with(['student', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalDepartments' => $totalDepartments,
            'totalCourses' => $totalCourses,
            'totalAttendanceRecords' => $totalAttendanceRecords,
            'presentAttendanceRecords' => $presentAttendanceRecords,
            'overallAttendancePercentage' => $overallAttendancePercentage,
            'recentStudents' => $recentStudents,
            'recentAssessments' => $recentAssessments,
        ]);
    }
}
