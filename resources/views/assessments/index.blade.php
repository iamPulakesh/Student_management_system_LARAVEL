@extends('layouts.app')

@section('title', 'Assessments & Marks')
@section('heading', 'Assessments & Marks')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Assessments Records</h3>
        @if ($routePrefix === 'teacher')
            <a href="{{ route('teacher.assessments.create', array_filter(['course_id' => $courseId])) }}" class="btn btn-primary">
                Record Assessment Mark
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route($routePrefix . '.assessments.index') }}" class="filter-bar">
        @if ($routePrefix === 'admin')
            <div class="form-group">
                <label for="department_id">Department <span class="required">*</span></label>
                <select id="department_id" name="department_id" required>
                    <option value="">Select Department...</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ (string) $departmentId === (string) $department->id ? 'selected' : '' }}>
                            {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
                <x-form-error field="department_id" />
            </div>
        @endif

        <div class="form-group">
            <label for="course_id">Course @if ($routePrefix === 'admin')<span class="required">*</span>@endif</label>
            <select id="course_id" name="course_id" {{ $routePrefix === 'admin' ? 'required' : '' }}>
                <option value="">{{ $routePrefix === 'admin' ? 'Select a Course...' : 'All Courses' }}</option>
                @if ($routePrefix !== 'admin' || $departmentId)
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->code }} — {{ $course->name }}@if ($routePrefix === 'admin') ({{ $course->department?->code }})@endif
                        </option>
                    @endforeach
                @endif
            </select>
            <x-form-error field="course_id" />
        </div>

        <div class="form-group">
            <label for="search">Search</label>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ $search }}"
                placeholder="Reg No or Course Code"
            >
            <x-form-error field="search" />
        </div>

        <div class="actions-group">
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if ($departmentId || $courseId || $search !== '')
                <a href="{{ route($routePrefix . '.assessments.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    @if ($routePrefix === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const departmentSelect = document.getElementById('department_id');
                const courseSelect = document.getElementById('course_id');

                if (!departmentSelect || !courseSelect) {
                    return;
                }

                const allCourses = @json($allCoursesData);

                departmentSelect.addEventListener('change', function () {
                    const selectedDepartmentId = departmentSelect.value;
                    const selectedCourseId = courseSelect.value;

                    courseSelect.innerHTML = '<option value="">Select a Course...</option>';

                    if (!selectedDepartmentId) {
                        return;
                    }

                    const filteredCourses = allCourses.filter(function (course) {
                        return String(course.department_id) === selectedDepartmentId;
                    });

                    filteredCourses.forEach(function (course) {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.code + ' — ' + course.name + (course.department_code ? ' (' + course.department_code + ')' : '');
                        option.selected = String(course.id) === selectedCourseId;
                        courseSelect.appendChild(option);
                    });
                });
            });
        </script>
    @endif

    @if ($routePrefix !== 'admin' || ($isFiltered ?? false))
        <div class="card-header">
                <h3 class="card-title">Recorded Assessments ({{ $assessments->total() }})</h3>
            </div>

        @if ($assessments->isEmpty())
            <div class="empty-state">No assessment records found matching the selected criteria.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Registration Number</th>
                            <th>Student Name</th>
                            <th>Assessment</th>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assessments as $assessment)
                            <tr>
                                <td>
                                    <strong>{{ $assessment->course?->code }}</strong>
                                    <div class="text-muted">{{ $assessment->course?->name }}</div>
                                </td>
                                <td>{{ $assessment->student?->student_number }}</td>
                                <td>{{ $assessment->student?->full_name }}</td>
                                <td>{{ $assessment->assessment_name }}</td>
                                <td>{{ $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ number_format((float) $assessment->marks_obtained, 2) }} / {{ number_format((float) $assessment->max_marks, 2) }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ number_format($assessment->percentage, 2) }}%
                                    </span>
                                </td>
                                <td>{{ $assessment->recordedBy?->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route($routePrefix . '.assessments.edit', $assessment) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route($routePrefix . '.assessments.destroy', $assessment) }}" onsubmit="return confirm('Are you sure you want to delete this assessment record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $assessments->links() }}
            </div>
        @endif
    @endif
@endsection
