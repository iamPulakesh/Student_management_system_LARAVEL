@extends('layouts.app')

@section('title', 'Attendance')
@section('heading', 'Attendance Management')

@section('content')
    @if ($routePrefix === 'teacher')
        <div class="alert alert-info">
            <strong>Prototype Scope:</strong> One attendance record is stored per student, course, and date (single daily session per course).
        </div>
    @endif

    <div class="card-header">
        <h3 class="card-title">Filter Attendance Records</h3>
        @if ($routePrefix === 'teacher')
            <a href="{{ route('teacher.attendance.create', array_filter(['course_id' => $courseId, 'attendance_date' => $attendanceDate])) }}" class="btn btn-primary">
                Record Attendance
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="filter-bar">
        <div class="form-group">
            <label for="course_id" class="form-label">Course @if ($routePrefix === 'admin')<span class="required">*</span>@endif</label>
            <select id="course_id" name="course_id" class="form-control" {{ $routePrefix === 'admin' ? 'required' : '' }}>
                <option value="">{{ $routePrefix === 'admin' ? 'Select a Course...' : 'All Accessible Courses' }}</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ (string) old('course_id', $courseId) === (string) $course->id ? 'selected' : '' }}>
                        {{ $course->code }} — {{ $course->name }} ({{ $course->department?->code }})
                    </option>
                @endforeach
            </select>
            <x-form-error field="course_id" />
        </div>

        <div class="form-group">
            <label for="attendance_date" class="form-label">Date @if ($routePrefix === 'admin')<span class="required">*</span>@endif</label>
            <input
                type="date"
                id="attendance_date"
                name="attendance_date"
                value="{{ old('attendance_date', $attendanceDate) }}"
                class="form-control"
                {{ $routePrefix === 'admin' ? 'required' : '' }}
            >
            <x-form-error field="attendance_date" />
        </div>

        <div class="actions-group">
            <button type="submit" class="btn btn-primary">Filter</button>
            @if ($courseId || $attendanceDate !== '')
                <a href="{{ route($routePrefix . '.attendance.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    @if ($selectedCourse !== null && ($routePrefix !== 'admin' || $attendanceDate !== ''))
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
                            <th>Status</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
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
                                <td>
                                    @if ($summary['attendance'])
                                        <span class="badge {{ $summary['attendance']->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($summary['attendance']->status) }}
                                        </span>
                                        @if ($attendanceDate === '' && $summary['attendance']->attendance_date)
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $summary['attendance']->attendance_date->format('Y-m-d') }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $summary['attendance']?->recordedBy?->name ?? '—' }}
                                </td>
                                <td>
                                    @if ($summary['attendance'])
                                        <div class="actions-group">
                                            @if ($routePrefix === 'admin')
                                                <a href="{{ route('admin.attendance.edit', $summary['attendance']) }}" class="btn btn-secondary btn-sm">Edit</a>
                                            @endif
                                            <form method="POST" action="{{ route($routePrefix . '.attendance.destroy', $summary['attendance']) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @elseif ($routePrefix === 'admin')

    @endif
@endsection
