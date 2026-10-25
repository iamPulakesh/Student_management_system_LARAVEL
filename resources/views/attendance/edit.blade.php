@extends('layouts.app')

@section('title', 'Edit Attendance Record')

@section('content')
        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="attendance_date">Attendance Date <span class="required">*</span></label>
                    <input
                        id="attendance_date"
                        type="date"
                        name="attendance_date"
                        value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                        max="{{ now()->toDateString() }}"
                        required
                    >
                    <x-form-error field="attendance_date" />
                </div>

                <div class="form-group">
                    <label for="status">Attendance Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="present" {{ old('status', $attendance->status) === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ old('status', $attendance->status) === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                    <x-form-error field="status" />
                </div>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn">Update</button>
                <a href="{{ route('admin.attendance.index', ['course_id' => $attendance->course_id, 'attendance_date' => $attendance->attendance_date->format('Y-m-d')]) }}" class="btn">
                    Cancel
                </a>
            </div>
        </form>
@endsection
