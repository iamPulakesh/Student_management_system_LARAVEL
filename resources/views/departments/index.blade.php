@extends('layouts.app')

@section('title', 'Departments')

@section('content')
    <div class="card-header">
        <h3 class="card-title">All Departments</h3>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Add Department</a>
    </div>

    <form method="GET" action="{{ route('admin.departments.index') }}" class="filter-bar">
        <div class="form-group">
            <label for="search" class="form-label">Search by Name or Code</label>
            <input
                id="search"
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="e.g. Computer Science or CSE"
                class="form-control"
            >
        </div>
        <div class="actions-group">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if ($departments->isEmpty())
        <div class="empty-state">No departments found.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Students</th>
                        <th>Teachers</th>
                        <th>Courses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td><strong>{{ $department->code }}</strong></td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->students_count }}</td>
                            <td>{{ $department->teachers_count }}</td>
                            <td>{{ $department->courses_count }}</td>
                            <td>
                                <div class="actions-group">
                                    <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-secondary btn-sm">View</a>
                                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Are you sure you want to delete this department?');">
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
            {{ $departments->links() }}
        </div>
    @endif
@endsection
