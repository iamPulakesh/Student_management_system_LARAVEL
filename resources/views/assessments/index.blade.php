@extends('layouts.app')

@section('title', 'Assessments & Marks')
@section('heading', 'Assessments & Marks')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Filter Assessments</h3>
        @if ($routePrefix === 'teacher')
            <a href="{{ route('teacher.assessments.create', array_filter(['course_id' => $courseId])) }}" class="btn btn-primary">
                Record Assessment Mark
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route($routePrefix . '.assessments.index') }}" class="filter-bar">
        <div class="form-group">
            <label for="course_id">Course</label>
            <select id="course_id" name="course_id">
                <option value="">All Accessible Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}>
                        {{ $course->code }} — {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="student_id">Student</label>
            <select id="student_id" name="student_id">
                <option value="">All Eligible Students</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" {{ (string) $studentId === (string) $student->id ? 'selected' : '' }}>
                        {{ $student->student_number }} — {{ $student->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="search">Search</label>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ $search }}"
                placeholder="Assessment name or student..."
            >
        </div>

        <div class="actions-group">
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if ($courseId || $studentId || $search !== '' || ($routePrefix === 'admin' && ($isFiltered ?? false)))
                <a href="{{ route($routePrefix . '.assessments.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

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
