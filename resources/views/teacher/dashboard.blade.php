@extends('layouts.app')

@section('title', 'Faculty Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Employee Code</div>
            <div class="stat-value" style="font-size: 1.4rem;">{{ $teacher->employee_number }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Assigned Courses</div>
            <div class="stat-value">{{ $assignedCoursesCount }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Attendance Entries</div>
            <div class="stat-value">{{ $totalAttendanceRecorded }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Assessments Recorded</div>
            <div class="stat-value">{{ $totalAssessmentsRecorded }}</div>
        </div>
    </div>
@endsection
