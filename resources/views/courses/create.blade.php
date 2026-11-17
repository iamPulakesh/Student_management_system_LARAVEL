@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Create Course</h3>
    </div>

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf
        @include('courses._form', ['submitLabel' => 'Create Course'])
    </form>
@endsection
