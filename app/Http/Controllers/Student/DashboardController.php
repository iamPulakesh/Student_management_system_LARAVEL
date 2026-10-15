<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->resolveAuthorizedStudent($request);

        $courses = Course::query()
            ->with(['teacher.user', 'department'])
            ->where('department_id', $student->department_id)
            ->orderBy('code')
            ->get();

        $totalAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->count();

        $presentAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->where('status', Attendance::STATUS_PRESENT)
            ->count();

        $absentAttendance = $totalAttendance - $presentAttendance;

        $attendancePercentage = $totalAttendance > 0
            ? round(($presentAttendance / $totalAttendance) * 100, 1)
            : null;

        $recentAttendances = Attendance::query()
            ->with('course')
            ->where('student_id', $student->id)
            ->latest('attendance_date')
            ->latest('id')
            ->take(10)
            ->get();

        $assessments = Assessment::query()
            ->with('course')
            ->where('student_id', $student->id)
            ->latest('assessment_date')
            ->latest('id')
            ->take(10)
            ->get();

        return view('student.dashboard', [
            'student' => $student,
            'courses' => $courses,
            'totalAttendance' => $totalAttendance,
            'presentAttendance' => $presentAttendance,
            'absentAttendance' => $absentAttendance,
            'attendancePercentage' => $attendancePercentage,
            'recentAttendances' => $recentAttendances,
            'assessments' => $assessments,
        ]);
    }

    public function updateProfile(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $student = $this->resolveAuthorizedStudent($request);
        $validated = $request->validated();

        $student->update([
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $student->user->update([
                'password' => $validated['password'],
            ]);
        }

        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Your profile details have been updated successfully.');
    }

    private function resolveAuthorizedStudent(Request $request): Student
    {
        return $request->user()->student()->with(['department', 'user'])->firstOrFail();
    }
}
