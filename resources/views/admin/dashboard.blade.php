@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('heading', 'Administrator Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-subtext"><a href="{{ route('admin.students.index') }}">Manage students</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Teachers</div>
            <div class="stat-value">{{ $totalTeachers }}</div>
            <div class="stat-subtext"><a href="{{ route('admin.teachers.index') }}">Manage teachers</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Departments</div>
            <div class="stat-value">{{ $totalDepartments }}</div>
            <div class="stat-subtext"><a href="{{ route('admin.departments.index') }}">Manage departments</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Courses</div>
            <div class="stat-value">{{ $totalCourses }}</div>
            <div class="stat-subtext"><a href="{{ route('admin.courses.index') }}">Manage courses</a></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Overall Attendance</div>
            <div class="stat-value">
                @if ($overallAttendancePercentage !== null)
                    {{ number_format($overallAttendancePercentage, 1) }}%
                @else
                    N/A
                @endif
            </div>
            <div class="stat-subtext">
                {{ $presentAttendanceRecords }} present of {{ $totalAttendanceRecords }} recorded entries
            </div>
        </div>
    </div>

    <div class="two-col-grid">
        <section class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Students</h3>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">Add Student</a>
            </div>

            @if ($recentStudents->isEmpty())
                <div class="empty-state">No students registered yet.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reg. #</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentStudents as $student)
                                <tr>
                                    <td><a href="{{ route('admin.students.show', $student) }}">{{ $student->student_number }}</a></td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->department?->code ?? 'N/A' }}</td>
                                    <td>Semester {{ $student->year_of_study }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <div class="card-header">
                <h3 class="card-title">Recently Recorded Assessments</h3>
                <a href="{{ route('admin.assessments.create') }}" class="btn btn-primary btn-sm">Record Marks</a>
            </div>

            @if ($recentAssessments->isEmpty())
                <div class="empty-state">No assessments recorded yet.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Assessment</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAssessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->student?->full_name ?? 'N/A' }}</td>
                                    <td>{{ $assessment->course?->code ?? 'N/A' }}</td>
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
