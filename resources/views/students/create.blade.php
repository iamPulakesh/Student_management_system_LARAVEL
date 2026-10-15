@extends('layouts.app')

@section('title', 'Create Student')
@section('heading', 'Create Student Account & Profile')

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            @include('students._form', ['submitLabel' => 'Create Student'])
        </form>
    </div>
@endsection
