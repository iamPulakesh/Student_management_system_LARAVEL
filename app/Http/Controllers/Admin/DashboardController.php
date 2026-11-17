<?php

/**
 * Admin dashboard with overall totals.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalDepartments' => $totalDepartments,
            'totalCourses' => $totalCourses,
        ]);
    }
}
