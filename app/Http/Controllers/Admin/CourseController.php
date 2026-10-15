<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $departmentId = $request->query('department_id');

        $courses = Course::query()
            ->with(['department', 'teacher.user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        $departments = Department::query()->orderBy('name')->get();

        return view('courses.index', [
            'courses' => $courses,
            'departments' => $departments,
            'search' => $search,
            'selectedDepartmentId' => $departmentId,
            'isTeacherView' => false,
        ]);
    }

    public function create(): View
    {
        return view('courses.create', [
            'course' => new Course(),
            'departments' => Department::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->with(['user', 'department'])->orderBy('employee_number')->get(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course): View
    {
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
            'isTeacherView' => false,
        ]);
    }

    public function edit(Course $course): View
    {
        return view('courses.edit', [
            'course' => $course,
            'departments' => Department::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->with(['user', 'department'])->orderBy('employee_number')->get(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['department_id'] !== (int) $course->department_id
            && ($course->attendances()->exists() || $course->assessments()->exists())
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'department_id' => 'Cannot change the department of a course that already has attendance or assessment records.',
                ]);
        }

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->attendances()->exists() || $course->assessments()->exists()) {
            return redirect()
                ->route('admin.courses.index')
                ->with('error', 'Cannot delete this course because attendance or assessment records refer to it.');
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
