{{--
    Admin page to create a student.
--}}

@extends('layouts.app')

@section('title', 'Create Student')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Create Student</h3>
    </div>

    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf
        @include('students._form', ['submitLabel' => 'Create'])
    </form>
@endsection
