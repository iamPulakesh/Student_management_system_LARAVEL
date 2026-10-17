<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $departmentId = $request->query('department_id');
        $yearOfStudy = $request->query('year_of_study');

        $hasFilter = $search !== ''
            || ($departmentId !== null && $departmentId !== '')
            || ($yearOfStudy !== null && $yearOfStudy !== '');

        $students = null;

        if ($hasFilter) {
            $students = Student::query()
                ->with(['department', 'user'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('student_number', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%");
                    });
                })
                ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
                ->when($yearOfStudy, fn ($query) => $query->where('year_of_study', $yearOfStudy))
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate(10)
                ->withQueryString();
        }

        $departments = Department::query()->orderBy('name')->get();

        return view('students.index', [
            'hasFilter' => $hasFilter,
            'students' => $students,
            'departments' => $departments,
            'search' => $search,
            'selectedDepartmentId' => $departmentId,
            'selectedYear' => $yearOfStudy,
        ]);
    }

    public function create(): View
    {
        return view('students.create', [
            'student' => new Student(),
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $fullName = trim("{$validated['first_name']} {$validated['last_name']}");

            $user = User::createWithRole([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => $validated['password'],
            ], User::ROLE_STUDENT);

            Student::create([
                'student_number' => $validated['student_number'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'department_id' => $validated['department_id'],
                'user_id' => $user->id,
                'year_of_study' => $validated['year_of_study'],
            ]);
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student profile and login account created successfully.');
    }

    public function show(Student $student): View
    {
        $student->load([
            'department',
            'user',
            'attendances' => fn ($query) => $query->with('course')->latest('attendance_date')->limit(20),
            'assessments' => fn ($query) => $query->with('course')->latest('assessment_date')->limit(20),
        ]);

        $totalAttendance = $student->attendances()->count();
        $presentAttendance = $student->attendances()->where('status', Attendance::STATUS_PRESENT)->count();
        $attendancePercentage = $totalAttendance > 0
            ? round(($presentAttendance / $totalAttendance) * 100, 1)
            : null;

        return view('students.show', [
            'student' => $student,
            'totalAttendance' => $totalAttendance,
            'presentAttendance' => $presentAttendance,
            'attendancePercentage' => $attendancePercentage,
        ]);
    }

    public function edit(Student $student): View
    {
        $student->load('user');

        return view('students.edit', [
            'student' => $student,
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['department_id'] !== (int) $student->department_id
            && ($student->attendances()->exists() || $student->assessments()->exists())
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'department_id' => 'Cannot change the department of a student who already has attendance or assessment records.',
                ]);
        }

        DB::transaction(function () use ($validated, $student): void {
            $fullName = trim("{$validated['first_name']} {$validated['last_name']}");

            $userAttributes = [
                'name' => $fullName,
                'email' => $validated['email'],
            ];

            if (! empty($validated['password'])) {
                $userAttributes['password'] = $validated['password'];
            }

            $student->user->update($userAttributes);

            $student->update([
                'student_number' => $validated['student_number'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'department_id' => $validated['department_id'],
                'year_of_study' => $validated['year_of_study'],
            ]);
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student profile updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->attendances()->exists() || $student->assessments()->exists()) {
            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Cannot delete this student because attendance or assessment records exist for them.');
        }

        DB::transaction(function () use ($student): void {
            $user = $student->user;
            $student->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student profile and account deleted successfully.');
    }
}
