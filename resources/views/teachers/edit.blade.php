@extends('layouts.app')

@section('title', 'Edit Teacher')
@section('heading', 'Edit Teacher Profile')

@section('content')
    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf
        @method('PUT')
        @include('teachers._form', ['submitLabel' => 'Update Teacher'])
    </form>
@endsection
