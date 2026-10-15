<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $departmentId = $request->query('department_id');

        $teachers = Teacher::query()
            ->with(['user', 'department'])
            ->withCount('courses')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('employee_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('employee_number')
            ->paginate(10)
            ->withQueryString();

        $departments = Department::query()->orderBy('name')->get();

        return view('teachers.index', [
            'teachers' => $teachers,
            'departments' => $departments,
            'search' => $search,
            'selectedDepartmentId' => $departmentId,
        ]);
    }

    public function create(): View
    {
        return view('teachers.create', [
            'teacher' => new Teacher(),
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'employee_number' => strtoupper(trim((string) $request->input('employee_number'))),
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'employee_number' => ['required', 'string', 'max:4', 'regex:/^[a-zA-Z0-9]+$/', 'unique:teachers,employee_number'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            $generatedEmail = strtolower($validated['employee_number']) . '@teacher.system';
            
            $user = User::createWithRole([
                'name' => $validated['name'],
                'email' => $generatedEmail,
                'password' => $validated['password'],
            ], User::ROLE_TEACHER);

            Teacher::create([
                'user_id' => $user->id,
                'employee_number' => $validated['employee_number'],
                'department_id' => $validated['department_id'],
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher profile and login account created successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'department', 'courses.department']);

        return view('teachers.show', [
            'teacher' => $teacher,
        ]);
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.edit', [
            'teacher' => $teacher,
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $request->merge([
            'employee_number' => strtoupper(trim((string) $request->input('employee_number'))),
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'employee_number' => [
                'required',
                'string',
                'max:4',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('teachers', 'employee_number')->ignore($teacher->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        DB::transaction(function () use ($validated, $teacher): void {
            $generatedEmail = strtolower($validated['employee_number']) . '@teacher.system';

            $userAttributes = [
                'name' => $validated['name'],
                'email' => $generatedEmail,
            ];

            if (! empty($validated['password'])) {
                $userAttributes['password'] = $validated['password'];
            }

            $teacher->user->update($userAttributes);

            $teacher->update([
                'employee_number' => $validated['employee_number'],
                'department_id' => $validated['department_id'],
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher profile updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $hasAssignedCourses = $teacher->courses()->exists();
        $hasRecordedAcademicData = $teacher->user
            && ($teacher->user->recordedAttendances()->exists() || $teacher->user->recordedAssessments()->exists());

        if ($hasAssignedCourses || $hasRecordedAcademicData) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Cannot delete this teacher because courses or recorded academic entries depend on them. Unassign their courses first.');
        }

        DB::transaction(function () use ($teacher): void {
            $user = $teacher->user;
            $teacher->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher profile and account deleted successfully.');
    }
}
