@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('heading', 'Teacher Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Employee Number</div>
            <div class="stat-value" style="font-size: 1.4rem;">{{ $teacher->employee_number }}</div>
            <div class="stat-subtext">{{ $teacher->department?->name ?? 'N/A' }} ({{ $teacher->department?->code ?? 'N/A' }})</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Assigned Courses</div>
            <div class="stat-value">{{ $assignedCourses->count() }}</div>
            <div class="stat-subtext"><a href="{{ route('teacher.courses.index') }}">View my courses</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Attendance Entries</div>
            <div class="stat-value">{{ $totalAttendanceRecorded }}</div>
            <div class="stat-subtext"><a href="{{ route('teacher.attendance.create') }}">Record attendance</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Assessments Recorded</div>
            <div class="stat-value">{{ $totalAssessmentsRecorded }}</div>
            <div class="stat-subtext"><a href="{{ route('teacher.assessments.create') }}">Enter marks</a></div>
        </div>
    </div>

    <section>
        <div class="card-header">
            <h3 class="card-title">My Assigned Courses</h3>
            <div class="actions-group">
                <a href="{{ route('teacher.attendance.create') }}" class="btn btn-primary btn-sm">Record Attendance</a>
                <a href="{{ route('teacher.assessments.create') }}" class="btn btn-secondary btn-sm">Record Assessment Marks</a>
            </div>
        </div>

        @if ($assignedCourses->isEmpty())
            <div class="empty-state">No courses are currently assigned to your teacher profile.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Credits</th>
                            <th>Eligible Students</th>
                            <th>Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignedCourses as $course)
                            <tr>
                                <td><strong>{{ $course->code }}</strong></td>
                                <td><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->name }}</a></td>
                                <td>{{ $course->department?->name }} ({{ $course->department?->code }})</td>
                                <td>{{ $course->credits }}</td>
                                <td>{{ $course->eligible_students_count ?? 0 }}</td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route('teacher.attendance.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">Attendance</a>
                                        <a href="{{ route('teacher.assessments.create', ['course_id' => $course->id]) }}" class="btn btn-secondary btn-sm">Add Marks</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="two-col-grid">
        <section>
            <div class="card-header">
                <h3 class="card-title">Recent Attendance Activity</h3>
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>

            @if ($recentAttendances->isEmpty())
                <div class="empty-state">No attendance records found for your courses.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Student</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAttendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                    <td>{{ $attendance->course?->code }}</td>
                                    <td>{{ $attendance->student?->full_name }}</td>
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

        <section>
            <div class="card-header">
                <h3 class="card-title">Recent Assessments</h3>
                <a href="{{ route('teacher.assessments.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>

            @if ($recentAssessments->isEmpty())
                <div class="empty-state">No assessments recorded for your courses yet.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Student</th>
                                <th>Assessment</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAssessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->course?->code }}</td>
                                    <td>{{ $assessment->student?->full_name }}</td>
                                    <td>{{ $assessment->assessment_name }}</td>
                                    <td>
                                        {{ number_format((float) $assessment->marks_obtained, 2) }} / {{ number_format((float) $assessment->max_marks, 2) }}
                                        ({{ number_format($assessment->percentage, 1) }}%)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
