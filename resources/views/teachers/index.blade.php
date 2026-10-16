@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Teachers</h3>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Add Teacher</a>
        </div>

        <form method="GET" action="{{ route('admin.teachers.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="search" class="form-label">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Employee #, name, or email"
                    class="form-control"
                >
            </div>

            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select id="department_id" name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) $selectedDepartmentId === (string) $department->id)>
                            {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        @if ($teachers->isEmpty())
            <div class="empty-state">No teacher profiles found.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Employee #</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Assigned Courses</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->employee_number }}</strong></td>
                                <td>{{ $teacher->user?->name }}</td>
                                <td>{{ $teacher->user?->email }}</td>
                                <td>{{ $teacher->department?->name }} ({{ $teacher->department?->code }})</td>
                                <td>{{ $teacher->courses_count }}</td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-secondary btn-sm">View</a>
                                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Are you sure you want to delete this teacher profile and account?');">
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
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
@endsection
