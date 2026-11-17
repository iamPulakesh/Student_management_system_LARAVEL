@extends('layouts.app')

@section('title', 'Student Portal')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Student Portal</h3>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Registration Number</div>
            <div class="stat-value" style="font-size: 1.35rem;">{{ $student->student_number }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Department</div>
            <div class="stat-value" style="font-size: 1.35rem;">{{ $student->department?->code ?? 'N/A' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Courses</div>
            <div class="stat-value">{{ $courses }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Attendance Summary</div>
            <div class="stat-value">
                @if ($attendancePercentage !== null)
                    {{ number_format($attendancePercentage, 1) }}%
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

    <section id="profile" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Profile </h3>
            <button type="button" id="edit-profile-btn" class="btn" onclick="toggleProfileEdit(true)">Update Profile</button>
        </div>

        <div id="profile-view-mode">
            <dl class="detail-list">
                <div class="detail-item">
                    <dt>Full Name</dt>
                    <dd>{{ $student->full_name }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Registration Number</dt>
                    <dd>{{ $student->student_number }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Email Address</dt>
                    <dd>{{ $student->user?->email }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Department</dt>
                    <dd>{{ $student->department?->name }} ({{ $student->department?->code }})</dd>
                </div>
                <div class="detail-item">
                    <dt>Semester</dt>
                    <dd>Semester {{ $student->year_of_study }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Gender</dt>
                    <dd>{{ $student->gender ?: 'Not provided' }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Phone</dt>
                    <dd>{{ $student->phone ?: 'Not provided' }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Date of Birth</dt>
                    <dd>{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'Not provided' }}</dd>
                </div>
                <div class="detail-item">
                    <dt>Address</dt>
                    <dd>{{ $student->address ?: 'Not provided' }}</dd>
                </div>
            </dl>
        </div>

        @include('student._profile_edit_form')
    </section>
@endsection
