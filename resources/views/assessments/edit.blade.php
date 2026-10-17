@extends('layouts.app')

@section('title', 'Edit Assessment Mark')
@section('heading', 'Edit Assessment Mark')

@section('content')
        <form method="POST" action="{{ route($routePrefix . '.assessments.update', $assessment) }}">
            @csrf
            @method('PUT')
            @include('assessments._form', ['submitLabel' => 'Update'])
        </form>
@endsection
