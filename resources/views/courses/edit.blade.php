@extends('layouts.app')

@section('title', 'Edit Course')
@section('heading', 'Edit Course')

@section('content')
    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf
        @method('PUT')
        @include('courses._form', ['submitLabel' => 'Update Course'])
    </form>
@endsection
