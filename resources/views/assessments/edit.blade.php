@extends('layouts.app')

@section('title', 'Edit Assessment Mark')
@section('heading', 'Edit Assessment Mark')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Assessment: {{ $assessment->assessment_name }} ({{ $assessment->student?->full_name }})</h3>
            <a href="{{ route($routePrefix . '.assessments.index') }}" class="btn btn-secondary btn-sm">Back to Assessments</a>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.assessments.update', $assessment) }}">
            @csrf
            @method('PUT')
            @include('assessments._form', ['submitLabel' => 'Update Assessment'])
        </form>
    </div>
@endsection
