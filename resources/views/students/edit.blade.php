@extends('layouts.app')

@section('title', 'Edit Student')
@section('heading', 'Edit Student Profile')

@section('content')
    
        <form method="POST" action="{{ route('admin.students.update', $student) }}">
            @csrf
            @method('PUT')
            @include('students._form', ['submitLabel' => 'Update'])
        </form>

@endsection
