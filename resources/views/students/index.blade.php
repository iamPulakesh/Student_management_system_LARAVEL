@extends('layouts.app')

@section('title', 'Students')
@section('heading', 'Students')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Students</h3>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">New Student</a>
        </div>

        <form method="GET" action="{{ route('admin.students.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="search" class="form-label">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Reg #, first/last name, or email"
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

            <div class="form-group">
                <label for="year_of_study" class="form-label">Year of Study</label>
                <select id="year_of_study" name="year_of_study" class="form-control">
                    <option value="">All Years</option>
                    @for ($year = 1; $year <= 6; $year++)
                        <option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>
                            Year {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        @if ($students->isEmpty())
            <div class="empty-state">No students found matching your criteria.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reg. #</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_number }}</strong></td>
                                <td><a href="{{ route('admin.students.show', $student) }}">{{ $student->full_name }}</a></td>
                                <td>{{ $student->user?->email }}</td>
                                <td>{{ $student->department?->name }} ({{ $student->department?->code }})</td>
                                <td>Year {{ $student->year_of_study }}</td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary btn-sm">View</a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this student profile and account?');">
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
                {{ $students->links() }}
            </div>
        @endif
    </div>
@endsection
