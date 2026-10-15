@extends('layouts.app')

@section('title', 'Create Course')
@section('heading', 'Create Course')

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.courses.store') }}">
            @csrf
            @include('courses._form', ['submitLabel' => 'Create Course'])
        </form>
    </div>
@endsection
