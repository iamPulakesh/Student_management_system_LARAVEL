@extends('layouts.app')

@section('title', 'Record Attendance')
@section('heading', 'Record Class Attendance')

@section('content')
    <div class="alert alert-info">
        <strong>Prototype Note:</strong> Only one attendance record per student, course, and date is allowed. Selecting a course loads all students belonging to that course's department.
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Step 1: Select Course &amp; Date</h3>
            <a href="{{ route($routePrefix . '.attendance.index') }}" class="btn btn-secondary btn-sm">Back to Attendance</a>
        </div>

        <form method="GET" action="{{ route($routePrefix . '.attendance.create') }}" class="filter-bar">
            <div class="form-group">
                <label for="course_id">Course <span class="required">*</span></label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Choose a Course --</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) optional($selectedCourse)->id === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->code }} — {{ $course->name }} ({{ $course->department?->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="attendance_date">Attendance Date <span class="required">*</span></label>
                <input type="date" id="attendance_date" name="attendance_date" value="{{ $attendanceDate }}" required>
            </div>

            <div class="actions-group">
                <button type="submit" class="btn btn-primary">Load Eligible Students</button>
            </div>
        </form>
    </div>

    @if ($selectedCourse !== null)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Step 2: Mark Attendance — {{ $selectedCourse->code }} ({{ $selectedCourse->name }})
                </h3>
                <span class="badge badge-info">Department: {{ $selectedCourse->department?->name }}</span>
            </div>

            @if ($existingAttendance->isNotEmpty())
                <div class="alert alert-warning">
                    Attendance has already been recorded for {{ $existingAttendance->count() }} student(s) in this course on {{ $attendanceDate }}. Duplicate submissions for the same student, course, and date will be rejected.
                </div>
            @endif

            @if ($students->isEmpty())
                <div class="empty-state">
                    No students found in the {{ $selectedCourse->department?->name }} department. Add students to this department before recording attendance.
                </div>
            @else
                <form method="POST" action="{{ route($routePrefix . '.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                    <div class="form-group" style="max-width: 280px;">
                        <label for="post_attendance_date">Confirm Attendance Date <span class="required">*</span></label>
                        <input
                            type="date"
                            id="post_attendance_date"
                            name="attendance_date"
                            value="{{ old('attendance_date', $attendanceDate) }}"
                            required
                        >
                        <x-form-error field="attendance_date" />
                        <x-form-error field="course_id" />
                        <x-form-error field="statuses" />
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Registration Number</th>
                                    <th>Student Name</th>
                                    <th>Semester</th>
                                    <th>Existing Status ({{ $attendanceDate }})</th>
                                    <th>Mark Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        $currentStatus = old("statuses.{$student->id}", $existingAttendance->get($student->id, 'present'));
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $student->student_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>Semester {{ $student->year_of_study }}</td>
                                        <td>
                                            @if ($existingAttendance->has($student->id))
                                                <span class="badge {{ $existingAttendance->get($student->id) === 'present' ? 'badge-success' : 'badge-danger' }}">
                                                    Already Marked: {{ ucfirst($existingAttendance->get($student->id)) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </td>
                                        <td>
                                            <label for="status_{{ $student->id }}" class="sr-only">
                                                Attendance status for {{ $student->full_name }}
                                            </label>
                                            <select id="status_{{ $student->id }}" name="statuses[{{ $student->id }}]" required>
                                                <option value="present" {{ $currentStatus === 'present' ? 'selected' : '' }}>Present</option>
                                                <option value="absent" {{ $currentStatus === 'absent' ? 'selected' : '' }}>Absent</option>
                                            </select>
                                            <x-form-error :field="'statuses.' . $student->id" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="actions-group" style="margin-top: 1.25rem;">
                        <button type="submit" class="btn btn-primary">Save Attendance Batch</button>
                        <a href="{{ route($routePrefix . '.attendance.index', ['course_id' => $selectedCourse->id]) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    @endif
@endsection
