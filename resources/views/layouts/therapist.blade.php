
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'TekioTask – Therapist')</title>
    <link rel="stylesheet" href="{{ asset('css/tekiotask.css') }}">
    <link rel="manifest" href="/manifest.json">
    @yield('extra-styles')
</head>
<body class="{{ implode(' ', array_filter([
    Auth::user()->setting('high_contrast') ? 'a11y-high-contrast' : '',
    Auth::user()->setting('large_buttons') ? 'a11y-large-buttons' : '',
    'text-size-' . Auth::user()->setting('text_size', 16),
])) }}">

<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">T</div>
            <span class="brand-name">TekioTask</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('therapist.dashboard') }}"
               class="nav-link {{ request()->routeIs('therapist.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('therapist.case-notes') }}"
               class="nav-link {{ request()->routeIs('therapist.case-notes') ? 'active' : '' }}">
                Case Notes
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="{{ route('profile.index') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            <div class="topbar-user">
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="role-badge therapist">Therapist</span>
            </div>
        </div>
        <div class="content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('js/pwa-register.js') }}"></script>
<script src="{{ asset('js/session-heartbeat.js') }}"></script>
@yield('scripts')
</body>
</html>
