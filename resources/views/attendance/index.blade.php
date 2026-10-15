@extends('layouts.app')

@section('title', 'Attendance')
@section('heading', 'Attendance Management')

@section('content')
    <div class="alert alert-info">
        <strong>Prototype Scope:</strong> One attendance record is stored per student, course, and date (single daily session per course).
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Attendance Records</h3>
            <a href="{{ route($routePrefix . '.attendance.create', array_filter(['course_id' => $courseId, 'attendance_date' => $attendanceDate])) }}" class="btn btn-primary">
                Record Attendance
            </a>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="filter-bar">
            <div class="form-group">
                <label for="course_id">Course</label>
                <select id="course_id" name="course_id">
                    <option value="">All Accessible Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->code }} — {{ $course->name }} ({{ $course->department?->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="attendance_date">Date</label>
                <input type="date" id="attendance_date" name="attendance_date" value="{{ $attendanceDate }}">
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-secondary">Apply Filter</button>
                @if ($courseId || $attendanceDate !== '')
                    <a href="{{ route($routePrefix . '.attendance.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    @if ($selectedCourse !== null)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Student Attendance Summary — {{ $selectedCourse->code }} ({{ $selectedCourse->name }})
                </h3>
                <span class="badge badge-info">Department: {{ $selectedCourse->department?->name }}</span>
            </div>

            @if ($studentSummaries->isEmpty())
                <div class="empty-state">No students are currently enrolled in {{ $selectedCourse->department?->name }}.</div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Present / Total</th>
                                <th>Attendance Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentSummaries as $summary)
                                <tr>
                                    <td><strong>{{ $summary['student']->student_number }}</strong></td>
                                    <td>{{ $summary['student']->full_name }}</td>
                                    <td>{{ $summary['present'] }} / {{ $summary['total'] }}</td>
                                    <td>
                                        @if ($summary['percentage'] !== null)
                                            <span class="badge {{ $summary['percentage'] >= 75 ? 'badge-success' : 'badge-warning' }}">
                                                {{ number_format($summary['percentage'], 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">N/A (0 records)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Attendance History ({{ $attendances->total() }})</h3>
        </div>

        @if ($attendances->isEmpty())
            <div class="empty-state">No attendance records found matching the selected criteria.</div>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Registration Number</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                                <td>
                                    <strong>{{ $attendance->course?->code }}</strong>
                                    <div class="text-muted">{{ $attendance->course?->name }}</div>
                                </td>
                                <td>{{ $attendance->student?->student_number }}</td>
                                <td>{{ $attendance->student?->full_name }}</td>
                                <td>
                                    <span class="badge {{ $attendance->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->recordedBy?->name ?? 'N/A' }}</td>
                                <td>
                                    <form method="POST" action="{{ route($routePrefix . '.attendance.destroy', $attendance) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
@endsection
