@extends('layouts.app')

@section('title', 'Record Assessment Mark')
@section('heading', 'Record Assessment Mark')

@section('content')
    <div class="card-header">
        <h3 class="card-title">New Assessment Result</h3>
        <a href="{{ route($routePrefix . '.assessments.index') }}" class="btn btn-secondary btn-sm">Back to Assessments</a>
    </div>

    <form method="POST" action="{{ route($routePrefix . '.assessments.store') }}">
        @csrf
        @include('assessments._form', ['submitLabel' => 'Record Assessment'])
    </form>
@endsection
