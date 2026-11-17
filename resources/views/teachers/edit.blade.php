@extends('layouts.app')

@section('title', 'Edit Faculty')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Edit Faculty</h3>
    </div>

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf
        @method('PUT')
        @include('teachers._form', ['submitLabel' => 'Update'])
    </form>
@endsection
