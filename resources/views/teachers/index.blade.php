{{--
    Admin list of faculty.
--}}

@extends('layouts.app')

@section('title', 'Faculty')

@section('content')
    <div class="card-header">
        <h3 class="card-title">All Faculty</h3>
        <a href="{{ route('admin.teachers.create') }}" class="btn">Add Faculty</a>
    </div>

    <form method="GET" action="{{ route('admin.teachers.index') }}" class="filter-bar">
        <div class="form-group">
            <label for="search">Search</label>
            <input
                id="search"
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Employee Code or First name"
            >
        </div>

        <div class="form-group">
            <label for="department_id">Department <span class="required">*</span></label>
            <select id="department_id" name="department_id" required>
                <option value="">Select Department...</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) $selectedDepartmentId === (string) $department->id)>
                        {{ $department->name }} ({{ $department->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="actions-group">
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('admin.teachers.index') }}" class="btn">Reset</a>
        </div>
    </form>

    @if ($hasFilter && $teachers !== null)
        @if ($teachers->isEmpty())
            <div class="empty-state">No faculty profiles found.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone No</th>
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
                                <td>{{ $teacher->phone ?: 'Not provided' }}</td>
                                <td>{{ $teacher->courses_count }}</td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn">View</a>
                                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn">Edit</a>
                                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Are you sure you want to delete this faculty profile and account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn">Delete</button>
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
    @endif
@endsection
