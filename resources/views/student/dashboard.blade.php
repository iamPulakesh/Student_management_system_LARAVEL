@extends('layouts.app')

@section('title', 'Student Portal')
@section('heading', 'Student Portal')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Registration Number</div>
            <div class="stat-value" style="font-size: 1.35rem;">{{ $student->student_number }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Department</div>
            <div class="stat-value" style="font-size: 1.35rem;">{{ $student->department?->code ?? 'N/A' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Department Courses</div>
            <div class="stat-value">{{ $courses->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Attendance Summary</div>
            <div class="stat-value">
                @if ($attendancePercentage !== null)
                    {{ number_format($attendancePercentage, 1) }}%
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

    <section id="profile" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Profile </h3>
            <button type="button" id="edit-profile-btn" class="btn btn-primary btn-sm" onclick="toggleProfileEdit(true)">Update Profile</button>
        </div>

        <div id="profile-view-mode">
            <dl class="detail-list">
                <div class="detail-item">
                    <dt>Full Name</dt>
                    <dd>{{ $student->full_name }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Registration Number</dt>
                    <dd>{{ $student->student_number }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Email Address</dt>
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
            </dl>
        </div>

        @include('student._profile_edit_form')
    </section>

    <section id="courses" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Courses for {{ $student->department?->name }}</h3>
        </div>

        @if ($courses->isEmpty())
            <div class="empty-state">No courses are currently listed for your department.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Assigned Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td><strong>{{ $course->code }}</strong></td>
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

    <div class="two-col-grid">
        <section id="attendance">
            <div class="card-header">
                <h3 class="card-title">My Attendance History</h3>
            </div>

            @if ($recentAttendances->isEmpty())
                <div class="empty-state">No attendance records have been recorded for you yet.</div>
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
                            @foreach ($recentAttendances as $record)
                                <tr>
                                    <td>{{ $record->attendance_date->format('Y-m-d') }}</td>
                                    <td>{{ $record->course?->code }} — {{ $record->course?->name }}</td>
                                    <td>
                                        <span class="badge {{ $record->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section id="marks">
            <div class="card-header">
                <h3 class="card-title">My Assessment Marks</h3>
            </div>

            @if ($assessments->isEmpty())
                <div class="empty-state">No assessment marks have been recorded for you yet.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assessment</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->course?->code }}</td>
                                    <td>{{ $assessment->assessment_name }}</td>
                                    <td>{{ $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : '—' }}</td>
                                    <td>{{ number_format((float) $assessment->marks_obtained, 2) }} / {{ number_format((float) $assessment->max_marks, 2) }}</td>
                                    <td><strong>{{ number_format($assessment->percentage, 2) }}%</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
