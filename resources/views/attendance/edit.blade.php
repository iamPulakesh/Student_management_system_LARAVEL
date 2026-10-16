@extends('layouts.app')

@section('title', 'Edit Attendance Record')
@section('heading', 'Edit Attendance Record')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Attendance Details</h3>
            <a href="{{ route('admin.attendance.index', ['course_id' => $attendance->course_id, 'attendance_date' => $attendance->attendance_date->format('Y-m-d')]) }}" class="btn btn-secondary btn-sm">
                Back to Attendance
            </a>
        </div>

        <dl class="detail-list">
            <div class="detail-item">
                <dt>Registration Number</dt>
                <dd><strong>{{ $attendance->student?->student_number }}</strong></dd>
            </div>
            <div class="detail-item">
                <dt>Student Name</dt>
                <dd>{{ $attendance->student?->full_name }}</dd>
            </div>
            <div class="detail-item">
                <dt>Course</dt>
                <dd>{{ $attendance->course?->code }} — {{ $attendance->course?->name }}</dd>
            </div>
            <div class="detail-item">
                <dt>Department</dt>
                <dd>{{ $attendance->course?->department?->name ?? 'N/A' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Originally Recorded By</dt>
                <dd>{{ $attendance->recordedBy?->name ?? 'N/A' }}</dd>
            </div>
            <div class="detail-item">
                <dt>Current Status</dt>
                <dd>
                    <span class="badge {{ $attendance->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Attendance</h3>
        </div>

        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="attendance_date" class="form-label">Attendance Date <span class="required">*</span></label>
                    <input
                        id="attendance_date"
                        type="date"
                        name="attendance_date"
                        value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                        class="form-control"
                        required
                    >
                    <x-form-error field="attendance_date" />
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Attendance Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="present" {{ old('status', $attendance->status) === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ old('status', $attendance->status) === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                    <x-form-error field="status" />
                </div>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-primary">Update Attendance</button>
                <a href="{{ route('admin.attendance.index', ['course_id' => $attendance->course_id, 'attendance_date' => $attendance->attendance_date->format('Y-m-d')]) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
