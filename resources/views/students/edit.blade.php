{{--
    Admin page to edit a student.
--}}

@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Edit Student</h3>
    </div>

    <form method="POST" action="{{ route('admin.students.update', $student) }}">
        @csrf
        @method('PUT')
        @include('students._form', ['submitLabel' => 'Update'])
    </form>
@endsection
