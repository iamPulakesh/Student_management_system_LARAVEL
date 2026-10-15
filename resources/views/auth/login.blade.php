@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
    <div class="auth-header">
        <h1>University SMS</h1>
        <p>Sign in with your university account credentials</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="form-group">
            <label for="role" class="form-label">Sign In As</label>
            <select id="role" name="role" class="form-control" required>
                <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <x-form-error field="role" />
        </div>

        <div class="form-group">
            <label for="login_id" id="login-id-label" class="form-label">Reg No</label>
            <input
                id="login_id"
                type="text"
                name="login_id"
                value="{{ old('login_id') }}"
                class="form-control"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your Reg No"
            >
            <x-form-error field="login_id" />
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            >
            <x-form-error field="password" />
        </div>

        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>

    <script>
        (function() {
            const roleSelect = document.getElementById('role');
            const loginLabel = document.getElementById('login-id-label');
            const loginInput = document.getElementById('login_id');

            const fieldConfig = {
                student:  { label: 'Registration No', placeholder: 'Enter your Reg No', type: 'text' },
                admin:    { label: 'Email', placeholder: 'Enter your email address', type: 'email' },
                teacher:  { label: 'Employee Code', placeholder: 'Enter your Employee Code', type: 'text' }
            };

            function updateLoginField() {
                const config = fieldConfig[roleSelect.value] || fieldConfig.student;
                loginLabel.textContent = config.label;
                loginInput.placeholder = config.placeholder;
                loginInput.type = config.type;
            }

            roleSelect.addEventListener('change', updateLoginField);

            // Set initial state based on selected role (handles old() values)
            updateLoginField();
        })();
    </script>
@endsection
