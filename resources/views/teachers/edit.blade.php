@extends('layouts.app')

@section('title', 'Edit Faculty')

@section('content')
    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf
        @method('PUT')
        @include('teachers._form', ['submitLabel' => 'Update'])
    </form>
@endsection
