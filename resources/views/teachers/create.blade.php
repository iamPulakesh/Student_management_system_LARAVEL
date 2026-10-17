@extends('layouts.app')

@section('title', 'Create Faculty')
@section('heading', 'Create Faculty Account & Profile')

@section('content')
    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf
        @include('teachers._form', ['submitLabel' => 'Create'])
    </form>
@endsection
