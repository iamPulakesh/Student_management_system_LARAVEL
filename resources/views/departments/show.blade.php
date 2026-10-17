@extends('layouts.app')

@section('title', $department->name)
@section('heading', 'Department Details: ' . $department->name)

@section('content')
    <div class="card-header">
        <h3 class="card-title">{{ $department->name }} ({{ $department->code }})</h3>
    </div>

    <dl class="detail-list" style="margin-bottom: 2rem;">
        <div class="detail-item">
            <dt>Students</dt>
            <dd>{{ $department->students_count }}</dd>
        </div>
        <div class="detail-item">
            <dt>Faculty</dt>
            <dd>{{ $department->teachers_count }}</dd>
        </div>
        <div class="detail-item">
            <dt>Courses</dt>
            <dd>{{ $department->courses_count }}</dd>
        </div>
    </dl>

    <div class="two-col-grid">
        <section>
            <div class="card-header">
                <h3 class="card-title">Courses in {{ $department->code }}</h3>
            </div>

            @if ($department->courses->isEmpty())
                <div class="empty-state">No courses assigned to this department.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Credits</th>
                                <th>Faculty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department->courses as $course)
                                <tr>
                                    <td><a href="{{ route('admin.courses.show', $course) }}">{{ $course->code }}</a></td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->credits }}</td>
                                    <td>{{ $course->teacher?->user?->name ?? 'Unassigned' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section>
            <div class="card-header">
                <h3 class="card-title">Students in {{ $department->code }}</h3>
            </div>

            @if ($department->students->isEmpty())
                <div class="empty-state">No students enrolled in this department.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department->students as $student)
                                <tr>
                                    <td><a href="{{ route('admin.students.show', $student) }}">{{ $student->student_number }}</a></td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->user?->email }}</td>
                                    <td>Semester {{ $student->year_of_study }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
