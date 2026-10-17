@extends('layouts.app')

@section('title', !empty($isTeacherView) ? 'My Courses' : 'Courses')
@section('heading', !empty($isTeacherView) ? 'My Assigned Courses' : 'Courses')

@section('content')
    <div class="card-header">
        <h3 class="card-title">{{ !empty($isTeacherView) ? 'Courses Assigned to Me' : 'All Courses' }}</h3>
        @if (empty($isTeacherView))
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Add Course</a>
        @endif
    </div>

    @if (empty($isTeacherView))
        <form method="GET" action="{{ route('admin.courses.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="search" class="form-label">Search by Code or Name</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="e.g. CS101 or Algorithms"
                    class="form-control"
                >
            </div>

            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select id="department_id" name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) ($selectedDepartmentId ?? '') === (string) $department->id)>
                            {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    @endif

    @if (!empty($isTeacherView) || ($hasFilter && $courses !== null))
        @if ($courses->isEmpty())
            <div class="empty-state">No courses found.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Credits</th>
                            <th>Assigned Faculty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td><strong>{{ $course->code }}</strong></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->department?->name }} ({{ $course->department?->code }})</td>
                                <td>{{ $course->credits }}</td>
                                <td>{{ $course->teacher?->user?->name ?? 'Unassigned' }}</td>
                                <td>
                                    @if (!empty($isTeacherView))
                                        <div class="actions-group">
                                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary btn-sm">Students</a>
                                            <a href="{{ route('teacher.attendance.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">Attendance</a>
                                            <a href="{{ route('teacher.assessments.create', ['course_id' => $course->id]) }}" class="btn btn-secondary btn-sm">Marks</a>
                                        </div>
                                    @else
                                        <div class="actions-group">
                                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary btn-sm">View</a>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm">Edit</a>
                                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $courses->links() }}
            </div>
        @endif
    @endif
@endsection
