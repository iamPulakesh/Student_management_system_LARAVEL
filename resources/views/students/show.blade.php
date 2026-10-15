@extends('layouts.app')

@section('title', $student->full_name)
@section('heading', 'Student Details: ' . $student->full_name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $student->full_name }} ({{ $student->student_number }})</h3>
            <div class="actions-group">
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-sm">Edit Student</a>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <dl class="detail-list">
            <div class="detail-item">
                <dt>Registration Number</dt>
                <dd>{{ $student->student_number }}</dd>
            </div>
            <div class="detail-item">
                <dt>Full Name</dt>
                <dd>{{ $student->full_name }}</dd>
            </div>
            <div class="detail-item">
                <dt>Email</dt>
                <dd>{{ $student->user?->email }}</dd>
            </div>
            <div class="detail-item">
                <dt>Department</dt>
                <dd>{{ $student->department?->name }} ({{ $student->department?->code }})</dd>
            </div>
            <div class="detail-item">
                <dt>Semester</dt>
                <dd>Semester {{ $student->year_of_study }}</dd>
            </div>
            <div class="detail-item">
                <dt>Gender</dt>
                <dd>{{ $student->gender ?: 'Not provided' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Phone</dt>
                <dd>{{ $student->phone ?: 'Not provided' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Date of Birth</dt>
                <dd>{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'Not provided' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Address</dt>
                <dd>{{ $student->address ?: 'Not provided' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Attendance Summary</dt>
                <dd>
                    @if ($attendancePercentage !== null)
                        {{ number_format($attendancePercentage, 1) }}% ({{ $presentAttendance }} / {{ $totalAttendance }})
                    @else
                        N/A (0 records)
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <div class="two-col-grid">
        <section class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Attendance</h3>
            </div>

            @if ($student->attendances->isEmpty())
                <div class="empty-state">No attendance records for this student.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($student->attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                    <td>{{ $attendance->course?->code }}</td>
                                    <td>
                                        <span class="badge {{ $attendance->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <div class="card-header">
                <h3 class="card-title">Assessment Marks</h3>
            </div>

            @if ($student->assessments->isEmpty())
                <div class="empty-state">No assessments recorded for this student.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assessment</th>
                                <th>Score</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($student->assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->course?->code }}</td>
                                    <td>{{ $assessment->assessment_name }}</td>
                                    <td>{{ number_format((float) $assessment->marks_obtained, 2) }} / {{ number_format((float) $assessment->max_marks, 2) }}</td>
                                    <td>{{ number_format($assessment->percentage, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
