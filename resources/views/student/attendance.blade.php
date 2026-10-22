@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
    <div class="card-header">
        <h3 class="card-title">My Attendance History</h3>
    </div>

    @if ($attendances->isEmpty())
        <div class="empty-state">No attendance records have been recorded for you yet.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Course</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $record)
                        <tr>
                            <td>{{ $record->attendance_date->format('Y-m-d') }}</td>
                            <td>{{ $record->course?->code }} — {{ $record->course?->name }}</td>
                            <td>
                                <span class="badge {{ $record->status === 'present' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $attendances->links() }}
        </div>
    @endif
@endsection
