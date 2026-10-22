@extends('layouts.app')

@section('title', 'Create Student')

@section('content')

        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            @include('students._form', ['submitLabel' => 'Create'])
        </form>

@endsection
