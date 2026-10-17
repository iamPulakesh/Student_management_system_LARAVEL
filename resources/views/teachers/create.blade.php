@extends('layouts.app')

@section('title', 'Create Teacher')
@section('heading', 'Create Teacher Account & Profile')

@section('content')
    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf
        @include('teachers._form', ['submitLabel' => 'Create Teacher'])
    </form>
@endsection
