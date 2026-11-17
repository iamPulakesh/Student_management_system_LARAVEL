@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Edit Course</h3>
    </div>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf
        @method('PUT')
        @include('courses._form', ['submitLabel' => 'Update'])
    </form>
@endsection
