@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Courses for {{ $student->department?->name }}</h3>
    </div>

    @if ($courses->isEmpty())
        <div class="empty-state">No courses are currently listed for your department.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Semester</th>
                        <th>Assigned Faculty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td><strong>{{ $course->code }}</strong></td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                            <td>Semester {{ $course->semester }}</td>
                            <td>{{ $course->teacher?->user?->name ?? 'Unassigned' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
