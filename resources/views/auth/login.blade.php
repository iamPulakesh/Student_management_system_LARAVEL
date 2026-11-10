<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - SMS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
<div class="auth-card">
    <h2>Student Management System</h2>

    @if (session('success'))
        <p role="status" class="alert alert-success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p role="alert" class="alert alert-danger">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <table cellpadding="6">
            <tr>
                <td><label for="role">Sign In As</label></td>
                <td>
                    <select id="role" name="role" required>
                        <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Faculty</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <div style="color: red;"><x-form-error field="role" /></div>
                </td>
            </tr>
            <tr>
                <td><label for="login_id" id="login-id-label">Reg No</label></td>
                <td>
                    <input
                        id="login_id"
                        type="text"
                        name="login_id"
                        value="{{ old('login_id') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your Reg No"
                    >
                    <div style="color: red;"><x-form-error field="login_id" /></div>
                </td>
            </tr>
            <tr>
                <td><label for="password">Password</label></td>
                <td>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >
                    <div style="color: red;"><x-form-error field="password" /></div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Sign In</button></td>
            </tr>
        </table>
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
</div>
</body>
</html>
