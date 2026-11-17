{{--
    Admin list of students.
--}}

@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="card-header">
            <h3 class="card-title">All Students</h3>
            <a href="{{ route('admin.students.create') }}" class="btn">Add Student</a>
        </div>

        <form method="GET" action="{{ route('admin.students.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="search">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Reg No or First name"
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

            <div class="form-group">
                <label for="year_of_study">Semester</label>
                <select id="year_of_study" name="year_of_study">
                    <option value="">Select Semester...</option>
                    @for ($sem = 1; $sem <= 8; $sem++)
                        <option value="{{ $sem }}" @selected((string) $selectedYear === (string) $sem)>
                            Semester {{ $sem }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn">Filter</button>
                <a href="{{ route('admin.students.index') }}" class="btn">Reset</a>
            </div>
        </form>

        @if ($hasFilter && $students !== null)
            @if ($students->isEmpty())
                <div class="empty-state">No students found matching your criteria.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reg No</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td><strong>{{ $student->student_number }}</strong></td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->user?->email }}</td>
                                    <td>{{ $student->department?->name }} ({{ $student->department?->code }})</td>
                                    <td>Semester {{ $student->year_of_study }}</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn">View</a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn">Edit</a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this student profile and account?');">
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
                    {{ $students->links() }}
                </div>
            @endif
        @endif
@endsection
