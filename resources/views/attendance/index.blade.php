{{--
    Attendance list for a course and date.
--}}

@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Attendance</h3>
        @if ($routePrefix === 'teacher')
            <a href="{{ route('teacher.attendance.create', array_filter(['course_id' => $courseId, 'attendance_date' => $attendanceDate])) }}" class="btn">
                Record Attendance
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="filter-bar">
        @if ($routePrefix === 'admin')
            <div class="form-group">
                <label for="department_id">Department <span class="required">*</span></label>
                <select id="department_id" name="department_id" required>
                    <option value="">Select Department...</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) old('department_id', $departmentId) === (string) $department->id)>
                            {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
                <x-form-error field="department_id" />
            </div>
        @endif

        <div class="form-group">
            <label for="course_id">Course <span class="required">*</span></label>
            <select id="course_id" name="course_id" required>
                <option value="">Select a Course...</option>
                @if ($routePrefix !== 'admin' || $departmentId)
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) old('course_id', $courseId) === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->code }} — {{ $course->name }} ({{ $course->department?->code }})
                        </option>
                    @endforeach
                @endif
            </select>
            <x-form-error field="course_id" />
        </div>

        <div class="form-group">
            <label for="attendance_date">Date <span class="required">*</span></label>
            <input
                type="date"
                id="attendance_date"
                name="attendance_date"
                value="{{ old('attendance_date', $attendanceDate) }}"
                max="{{ now()->toDateString() }}"
                required
            >
            <x-form-error field="attendance_date" />
        </div>

        <div class="actions-group">
            <button type="submit" class="btn">Filter</button>
            @if ($courseId || $attendanceDate !== '' || ($routePrefix === 'admin' && $departmentId))
                <a href="{{ route($routePrefix . '.attendance.index') }}" class="btn">Reset</a>
            @endif
        </div>
    </form>

    @if ($selectedCourse !== null && $attendanceDate !== '')
        @if ($studentSummaries->isEmpty())
            <div class="empty-state">No students are currently enrolled in {{ $selectedCourse->department?->name }}, Semester {{ $selectedCourse->semester }}.</div>
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
                            @if ($routePrefix === 'admin')
                                <th>Actions</th>
                            @endif
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
                                        <span class="badge {{ $summary['percentage'] >= 75 ? 'badge-success' : '' }}">
                                            {{ number_format($summary['percentage'], 1) }}%
                                        </span>
                                    @else
                                        <span>N/A (0 records)</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($summary['attendance'])
                                        <span class="badge {{ $summary['attendance']->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($summary['attendance']->status) }}
                                        </span>
                                    @else
                                        <span>—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $summary['attendance']?->recordedBy?->name ?? '—' }}
                                </td>
                                @if ($routePrefix === 'admin')
                                    <td>
                                        @if ($summary['attendance'])
                                            <div class="actions-group">
                                                <a href="{{ route('admin.attendance.edit', $summary['attendance']) }}" class="btn">Edit</a>
                                                <form method="POST" action="{{ route('admin.attendance.destroy', $summary['attendance']) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn">Delete</button>
                                                </form>
                                            </div>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @if ($routePrefix === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deptSelect = document.getElementById('department_id');
                const courseSelect = document.getElementById('course_id');

                if (!deptSelect || !courseSelect) {
                    return;
                }

                const allCourses = @json($allCoursesData);

                function updateCourses() {
                    const selectedDept = deptSelect.value;
                    const selectedCourseId = courseSelect.value;

                    courseSelect.innerHTML = '<option value="">Select a Course...</option>';

                    if (!selectedDept) {
                        return;
                    }

                    const filtered = allCourses.filter(function (c) {
                        return String(c.department_id) === String(selectedDept);
                    });

                    let courseFound = false;

                    filtered.forEach(function (c) {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.code + ' — ' + c.name + (c.department_code ? ' (' + c.department_code + ')' : '');
                        if (String(c.id) === String(selectedCourseId)) {
                            opt.selected = true;
                            courseFound = true;
                        }
                        courseSelect.appendChild(opt);
                    });

                    if (!courseFound && selectedCourseId) {
                        courseSelect.value = '';
                    }
                }

                deptSelect.addEventListener('change', updateCourses);
            });
        </script>
    @endif
@endsection
