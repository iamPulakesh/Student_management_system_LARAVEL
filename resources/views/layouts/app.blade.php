<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'University SMS') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
</head>
<body class="{{ auth()->check() ? 'role-' . auth()->user()->role : '' }}">
    @auth
        <div class="app-layout">
            <header class="topbar" aria-label="Primary Navigation">
                <div class="topbar-inner">
                    <h2 class="topbar-title">@yield('heading', 'Dashboard')</h2>

                    @if (auth()->user()->isAdmin() || auth()->user()->isTeacher())
                        <nav class="nav-links">
                            <ul>
                                @if (auth()->user()->isAdmin())
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">Students</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.teachers.index') }}" class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">Teachers</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">Departments</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">Courses</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">Attendance</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.assessments.index') }}" class="{{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}">Assessments</a>
                                    </li>
                                @elseif (auth()->user()->isTeacher())
                                    <li>
                                        <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('teacher.courses.index') }}" class="{{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">My Courses</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('teacher.attendance.index') }}" class="{{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">Attendance</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('teacher.assessments.index') }}" class="{{ request()->routeIs('teacher.assessments.*') ? 'active' : '' }}">Assessments / Marks</a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif

                    <div class="user-action-group">
                        <div class="user-details">
                            <span class="user-display-name">{{ auth()->user()->name }}</span>
                            <span class="user-display-meta">{{ auth()->user()->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Sign Out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="content-container">
                @include('components.flash-message')
                @yield('content')
            </main>
        </div>

        <!-- Stop redirecting to previous page after logout -->
        <script>
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
                    window.location.reload();
                }
            });
        </script>
    @else
        <main class="auth-shell">
            <div class="auth-card">
                @include('components.flash-message')
                @yield('content')
            </div>
        </main>
    @endauth
</body>
</html>
