@extends('layouts.app')

@section('title', 'Record Assessment Mark')

@section('content')
    <div class="card-header">
        <h3 class="card-title">New Assessment</h3>
    </div>

    <form method="POST" action="{{ route($routePrefix . '.assessments.store') }}">
        @csrf
        @include('assessments._form', ['submitLabel' => 'Record Assessment'])
    </form>
@endsection
