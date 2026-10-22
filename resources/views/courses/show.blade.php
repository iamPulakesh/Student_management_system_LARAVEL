@extends('layouts.app')

@section('title', $course->code . ' — ' . $course->name)

@section('content')

    @if ($students->isEmpty())
        <div class="empty-state">No students are currently enrolled in this course's department.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td><strong>{{ $student->student_number }}</strong></td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->user?->email }}</td>
                            <td>Semester {{ $student->year_of_study }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $students->links() }}
        </div>
    @endif
@endsection
