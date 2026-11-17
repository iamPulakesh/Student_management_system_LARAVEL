{{--
    Page to edit an assessment mark.
--}}

@extends('layouts.app')

@section('title', 'Edit Assessment Mark')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Edit Assessment Mark</h3>
    </div>

    <form method="POST" action="{{ route($routePrefix . '.assessments.update', $assessment) }}">
        @csrf
        @method('PUT')
        @include('assessments._form', ['submitLabel' => 'Update'])
    </form>
@endsection
