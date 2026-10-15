@extends('layouts.app')

@section('title', $teacher->user?->name ?? 'Teacher Details')
@section('heading', 'Teacher Details')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $teacher->user?->name }} ({{ $teacher->employee_number }})</h3>
            <div class="actions-group">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary btn-sm">Edit Teacher</a>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <dl class="detail-list">
            <div class="detail-item">
                <dt>Employee Number</dt>
                <dd>{{ $teacher->employee_number }}</dd>
            </div>
            <div class="detail-item">
                <dt>Full Name</dt>
                <dd>{{ $teacher->user?->name }}</dd>
            </div>
            <div class="detail-item">
                <dt>Email Address</dt>
                <dd>{{ $teacher->user?->email }}</dd>
            </div>
            <div class="detail-item">
                <dt>Department</dt>
                <dd>{{ $teacher->department?->name }} ({{ $teacher->department?->code }})</dd>
            </div>
            <div class="detail-item">
                <dt>Phone</dt>
                <dd>{{ $teacher->phone ?: 'Not provided' }}</dd>
            </div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Assigned Courses</h3>
        </div>

        @if ($teacher->courses->isEmpty())
            <div class="empty-state">No courses assigned to this teacher yet.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teacher->courses as $course)
                            <tr>
                                <td><a href="{{ route('admin.courses.show', $course) }}">{{ $course->code }}</a></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->department?->name }} ({{ $course->department?->code }})</td>
                                <td>{{ $course->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
