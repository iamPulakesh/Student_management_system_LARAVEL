@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $totalStudents }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Faculty</div>
            <div class="stat-value">{{ $totalTeachers }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Departments</div>
            <div class="stat-value">{{ $totalDepartments }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Courses</div>
            <div class="stat-value">{{ $totalCourses }}</div>
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
        </div>
    </div>
@endsection
