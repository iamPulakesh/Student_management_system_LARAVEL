@extends('layouts.app')

@section('title', 'My Assessments')

@section('content')
    <div class="card-header">
        <h3 class="card-title">My Assessment Marks</h3>
    </div>

    @if ($assessments->isEmpty())
        <div class="empty-state">No assessment marks have been recorded for you yet.</div>
    @else
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Assessment</th>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessments as $assessment)
                        <tr>
                            <td><strong>{{ $assessment->course?->code }}</strong><div class="text-muted">{{ $assessment->course?->name }}</div></td>
                            <td>{{ $assessment->assessment_name }}</td>
                            <td>{{ $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : '—' }}</td>
                            <td>{{ $assessment->marks_obtained }} / {{ $assessment->max_marks }}</td>
                            <td><strong>{{ number_format($assessment->percentage, 2) }}%</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $assessments->links() }}
        </div>
    @endif
@endsection
