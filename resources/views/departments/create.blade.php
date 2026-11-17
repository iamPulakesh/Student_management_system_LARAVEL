{{--
    Admin page to create a department.
--}}

@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Create Department</h3>
    </div>

    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        @include('departments._form', ['submitLabel' => 'Create'])
    </form>
@endsection
