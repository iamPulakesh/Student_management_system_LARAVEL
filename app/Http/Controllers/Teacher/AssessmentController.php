<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments with optional course/student/search filters.
     */
    public function index(Request $request): View
    {
        $routePrefix = $this->routePrefix($request);
        $hasAssessmentFilters = $request->hasAny(['department_id', 'course_id', 'search']);
        $filterRequiredRule = $request->user()->isAdmin() && $hasAssessmentFilters ? 'required' : 'nullable';
        $courseExistsRule = Rule::exists('courses', 'id');

        if ($request->user()->isAdmin() && $hasAssessmentFilters) {
            $courseExistsRule->where('department_id', $request->input('department_id'));
        }

        $filters = $request->validate([
            'department_id' => [$filterRequiredRule, 'integer', 'exists:departments,id'],
            'course_id' => [$filterRequiredRule, 'integer', $courseExistsRule],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $departmentId = $request->user()->isAdmin() ? ($filters['department_id'] ?? null) : null;
        $allCourses = $this->allowedCoursesQuery($request)->get();
        $courses = $allCourses
            ->when($departmentId, fn ($items) => $items->where('department_id', (int) $departmentId))
            ->values();
        $allowedCourseIds = $allCourses->pluck('id');
        $courseId = $filters['course_id'] ?? null;
        $search = trim($filters['search'] ?? '');
        $selectedCourse = null;

        if ($courseId !== null && $courseId !== '') {
            $selectedCourse = Course::with('department')->findOrFail((int) $courseId);
            $this->authorizeCourseAccess($request, $selectedCourse);
        }

        $query = Assessment::with(['student.department', 'course.department', 'recordedBy']);

        if ($request->user()->isTeacher()) {
            $query->whereIn('course_id', $allowedCourseIds);
        }

        if ($departmentId !== null) {
            $query->whereHas('course', fn ($courseQuery) => $courseQuery->where('department_id', $departmentId));
        }

        if ($selectedCourse !== null) {
            $query->where('course_id', $selectedCourse->id);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('student_number', 'like', "%{$search}%");
                })
                    ->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('code', 'like', "%{$search}%");
                    });
            });
        }

        $isFiltered = $departmentId !== null || $courseId !== null || $search !== '';

        $assessments = ! $isFiltered
            ? Assessment::whereRaw('1 = 0')->paginate(15)
            : $query->orderByDesc('assessment_date')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('assessments.index', [
            'routePrefix' => $routePrefix,
            'departments' => $request->user()->isAdmin()
                ? Department::query()->orderBy('name')->get()
                : collect(),
            'departmentId' => $departmentId,
            'courses' => $courses,
            'allCoursesData' => $allCourses->map(fn (Course $course) => [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
                'department_id' => $course->department_id,
                'department_code' => $course->department?->code,
            ])->values(),
            'selectedCourse' => $selectedCourse,
            'courseId' => $courseId,
            'search' => $search,
            'assessments' => $assessments,
            'isFiltered' => $isFiltered,
        ]);
    }

    /**
     * Show the form for creating a new assessment mark.
     */
    public function create(Request $request): View
    {
        if ($request->user()->isAdmin()) {
            abort(403, 'Admins cannot record assessments.');
        }

        $routePrefix = $this->routePrefix($request);
        $courses = $this->allowedCoursesQuery($request)->get();

        $courseId = $request->input('course_id');
        $selectedCourse = null;

        if ($courseId !== null && $courseId !== '') {
            $selectedCourse = Course::with('department')->findOrFail((int) $courseId);
            $this->authorizeCourseAccess($request, $selectedCourse);
        }

        $students = $this->eligibleStudentsQuery($request, $courses, $selectedCourse)->get();

        return view('assessments.create', [
            'routePrefix' => $routePrefix,
            'courses' => $courses,
            'students' => $students,
            'selectedCourse' => $selectedCourse,
        ]);
    }

    /**
     * Store a newly created assessment mark in storage.
     */
    public function store(StoreAssessmentRequest $request): RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            abort(403, 'Admins cannot record assessments.');
        }

        $validated = $request->validated();
        $course = Course::findOrFail((int) $validated['course_id']);
        $this->authorizeCourseAccess($request, $course);

        Assessment::create([
            'course_id' => $course->id,
            'student_id' => $validated['student_id'],
            'assessment_name' => $validated['assessment_name'],
            'marks_obtained' => $validated['marks_obtained'],
            'max_marks' => $validated['max_marks'],
            'assessment_date' => $validated['assessment_date'] ?? null,
            'recorded_by_user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route($this->routePrefix($request).'.assessments.index', ['course_id' => $course->id])
            ->with('success', 'Assessment mark recorded successfully.');
    }

    /**
     * Show the form for editing the specified assessment mark.
     */
    public function edit(Request $request, Assessment $assessment): View
    {
        $assessment->load(['course.department', 'student.department']);
        $course = $assessment->course;

        if ($course === null) {
            abort(404);
        }

        $this->authorizeCourseAccess($request, $course);

        $routePrefix = $this->routePrefix($request);
        $courses = $this->allowedCoursesQuery($request)->get();
        $students = $this->eligibleStudentsQuery($request, $courses, null)->get();

        return view('assessments.edit', [
            'routePrefix' => $routePrefix,
            'assessment' => $assessment,
            'courses' => $courses,
            'students' => $students,
            'selectedCourse' => $course,
        ]);
    }

    /**
     * Update the specified assessment mark in storage.
     */
    public function update(UpdateAssessmentRequest $request, Assessment $assessment): RedirectResponse
    {
        $existingCourse = $assessment->course;
        if ($existingCourse === null) {
            abort(404);
        }

        $this->authorizeCourseAccess($request, $existingCourse);

        $validated = $request->validated();
        $newCourse = Course::findOrFail((int) $validated['course_id']);
        $this->authorizeCourseAccess($request, $newCourse);

        $assessment->update([
            'course_id' => $newCourse->id,
            'student_id' => $validated['student_id'],
            'assessment_name' => $validated['assessment_name'],
            'marks_obtained' => $validated['marks_obtained'],
            'max_marks' => $validated['max_marks'],
            'assessment_date' => $validated['assessment_date'] ?? null,
            'recorded_by_user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route($this->routePrefix($request).'.assessments.index', [
                'department_id' => $newCourse->department_id,
                'course_id' => $newCourse->id,
            ])
            ->with('success', 'Assessment mark updated successfully.');
    }

    /**
     * Remove the specified assessment mark from storage.
     */
    public function destroy(Request $request, Assessment $assessment): RedirectResponse
    {
        $course = $assessment->course;
        if ($course === null) {
            abort(404);
        }

        $this->authorizeCourseAccess($request, $course);

        $assessment->delete();

        return redirect()
            ->route($this->routePrefix($request).'.assessments.index')
            ->with('success', 'Assessment mark deleted successfully.');
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
     * Get the query builder for students eligible for the user's courses.
     */
    private function eligibleStudentsQuery(Request $request, $courses, ?Course $selectedCourse): Builder
    {
        $query = Student::with('department')->orderBy('student_number');

        if ($selectedCourse !== null) {
            return $query->where('department_id', $selectedCourse->department_id);
        }

        if ($request->user()->isTeacher()) {
            $departmentIds = $courses->pluck('department_id')->unique()->values();

            return $query->whereIn('department_id', $departmentIds);
        }

        return $query;
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
            abort(403, 'You are not authorized to access assessments for this course.');
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
