@extends('layouts.app')

@section('title', 'Edit Department')
@section('heading', 'Edit Department')

@section('content')
    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
        @csrf
        @method('PUT')
        @include('departments._form', ['submitLabel' => 'Update'])
    </form>
@endsection
