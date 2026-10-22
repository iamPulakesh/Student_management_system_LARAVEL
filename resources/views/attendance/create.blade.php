@extends('layouts.app')

@section('title', 'Record Attendance')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Select Course &amp; Date</h3>
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
            <input type="date" id="attendance_date" name="attendance_date" value="{{ $attendanceDate }}" max="{{ now()->toDateString() }}" required>
        </div>

        <div class="actions-group">
            <button type="submit" class="btn btn-primary">Load Students</button>
        </div>
    </form>

    @if ($selectedCourse !== null)
        <div class="card-header" style="margin-top: 2rem;">
            <h3 class="card-title">
            Mark Attendance
            </h3>

        </div>

            @if ($existingAttendance->isNotEmpty())
                <div class="alert alert-warning">
                    Attendance has already been recorded in this course on {{ $attendanceDate }}. Please contact admin for rectification.
                </div>
            @endif

            @if ($students->isEmpty())
                <div class="empty-state">
                    No students found in the {{ $selectedCourse->department?->name }} department. Add students to this department before recording attendance.
                </div>
            @else
                @php
                    $unmarkedStudents = $students->filter(fn($s) => !$existingAttendance->has($s->id));
                @endphp

                @if ($unmarkedStudents->isEmpty())
                    {{-- All students already marked, nothing to do --}}
                @else
                <form method="POST" action="{{ route($routePrefix . '.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                    <input type="hidden" name="attendance_date" value="{{ old('attendance_date', $attendanceDate) }}">
                    <x-form-error field="attendance_date" />
                    <x-form-error field="course_id" />
                    <x-form-error field="statuses" />

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Registration Number</th>
                                    <th>Student Name</th>
                                    <th>Semester</th>
                                    <th>Mark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unmarkedStudents as $student)
                                    @php
                                        $currentStatus = old("statuses.{$student->id}", 'present');
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $student->student_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>Semester {{ $student->year_of_study }}</td>
                                        <td>
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
            @endif
    @endif
@endsection
