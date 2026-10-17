@extends('layouts.app')

@section('title', $teacher->user?->name ?? 'Faculty Details')
@section('heading', 'Faculty Details')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Assigned Courses</h3>
    </div>

    @if ($teacher->courses->isEmpty())
        <div class="empty-state">No courses assigned to this faculty member yet.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teacher->courses as $course)
                        <tr>
                            <td><strong>{{ $course->code }}</strong></td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->department?->name }} ({{ $course->department?->code }})</td>
                            <td>{{ $course->credits }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
