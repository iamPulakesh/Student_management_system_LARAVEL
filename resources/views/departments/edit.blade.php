{{--
    Admin page to edit a department.
--}}

@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Edit Department</h3>
    </div>

    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
        @csrf
        @method('PUT')
        @include('departments._form', ['submitLabel' => 'Update'])
    </form>
@endsection
