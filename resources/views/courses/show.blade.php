@extends('layouts.app')

@section('title', $course->code . ' — ' . $course->name)

@section('content')
    <div class="card-header">
        <h3 class="card-title">{{ $course->code }} — {{ $course->name }}</h3>
        <div class="actions-group">
            @if (!empty($isTeacherView))
                <a href="{{ route('teacher.attendance.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">Record Attendance</a>
                <a href="{{ route('teacher.assessments.create', ['course_id' => $course->id]) }}" class="btn btn-secondary btn-sm">Record Marks</a>
                <a href="{{ route('teacher.courses.index') }}" class="btn btn-secondary btn-sm">Back to My Courses</a>
            @endif
        </div>
    </div>

    <dl class="detail-list" style="margin-bottom: 2rem;">
        <div class="detail-item">
            <dt>Course Code</dt>
            <dd>{{ $course->code }}</dd>
        </div>
        <div class="detail-item">
            <dt>Course Name</dt>
            <dd>{{ $course->name }}</dd>
        </div>
        <div class="detail-item">
            <dt>Department</dt>
            <dd>{{ $course->department?->name }} ({{ $course->department?->code }})</dd>
        </div>
        <div class="detail-item">
            <dt>Credits</dt>
            <dd>{{ $course->credits }}</dd>
        </div>
        <div class="detail-item">
            <dt>Semester</dt>
            <dd>Semester {{ $course->semester }}</dd>
        </div>
        <div class="detail-item">
            <dt>Assigned Faculty</dt>
            <dd>{{ $course->teacher?->user?->name ?? 'Unassigned' }}</dd>
        </div>
    </dl>

    <div class="card-header">
        <h3 class="card-title">Students ({{ $course->department?->name }})</h3>
    </div>

    @if ($students->isEmpty())
        <div class="empty-state">No students are currently enrolled in this course's department.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td><strong>{{ $student->student_number }}</strong></td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->user?->email }}</td>
                            <td>Semester {{ $student->year_of_study }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $students->links() }}
        </div>
    @endif
@endsection
