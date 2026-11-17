<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="{{ auth()->check() ? 'role-' . auth()->user()->role : '' }}">
    @auth
        <div class="app-layout">
            <header class="topbar" aria-label="Primary Navigation">
                <div class="topbar-inner">
                    <a href="{{ route('home') }}" class="brand" aria-label="SMS home">
                        <svg class="brand-mark" viewBox="0 0 18 18" aria-hidden="true">
                            <rect x="1" y="1" width="7" height="7" rx="1.5" fill="#e9ecf2"/>
                            <rect x="10" y="1" width="7" height="7" rx="1.5" fill="#7aa5f5"/>
                            <rect x="1" y="10" width="7" height="7" rx="1.5" fill="#4fcf7a"/>
                            <rect x="10" y="10" width="7" height="7" rx="1.5" fill="#e9ecf2"/>
                        </svg>
                        SMS
                    </a>

                    @if (auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->isStudent())
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
                                        <a href="{{ route('admin.teachers.index') }}" class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">Faculty</a>
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
                                        <a href="{{ route('teacher.assessments.index') }}" class="{{ request()->routeIs('teacher.assessments.*') ? 'active' : '' }}">Assessments</a>
                                    </li>
                                @elseif (auth()->user()->isStudent())
                                    <li>
                                        <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('student.courses') }}" class="{{ request()->routeIs('student.courses') ? 'active' : '' }}">Courses</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('student.attendance') }}" class="{{ request()->routeIs('student.attendance') ? 'active' : '' }}">Attendance</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('student.assessments') }}" class="{{ request()->routeIs('student.assessments') ? 'active' : '' }}">Assessments</a>
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
                            <button type="submit" class="btn">Sign Out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="content-container">
                @include('components.flash-message')
                @yield('content')
            </main>
        </div>

        <!-- Stop redirecting to previous page after logout and enforce max today on date inputs -->
        <script>
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
                    window.location.reload();
                }
            });

            function enforceMaxDateOnInputs() {
                const today = new Date().toLocaleDateString('en-CA');
                document.querySelectorAll('input[type="date"]').forEach(function (input) {
                    if (!input.getAttribute('max') || input.getAttribute('max') > today) {
                        input.setAttribute('max', today);
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', enforceMaxDateOnInputs);
            window.addEventListener('pageshow', enforceMaxDateOnInputs);
        </script>
    @endauth
</body>
</html>
