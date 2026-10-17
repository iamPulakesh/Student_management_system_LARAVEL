@extends('layouts.app')

@section('title', 'Create Department')
@section('heading', 'Create Department')

@section('content')
    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        @include('departments._form', ['submitLabel' => 'Create'])
    </form>
@endsection
