@extends('layouts.app')

@section('title', 'Create Faculty')

@section('content')
    <div class="card-header">
        <h3 class="card-title">Create Faculty</h3>
    </div>

    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf
        @include('teachers._form', ['submitLabel' => 'Create'])
    </form>
@endsection
