
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login – TekioTask</title>
    <link rel="stylesheet" href="{{ asset('css/tekiotask.css') }}">
    <link rel="manifest" href="/manifest.json">
</head>
<body class="login-body">

    <div class="login-brand">
        <div class="brand-icon">T</div>
        <span class="brand-name">TekioTask</span>
    </div>

    <div class="login-card">
        <h2>Welcome Back</h2>
        <p>Select your role to continue</p>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="role-grid">
                @php
                    $roles = [
                        ['value' => 'Administrator', 'label' => 'Admin'],
                        ['value' => 'Teacher',       'label' => 'Teacher'],
                        ['value' => 'Therapist',     'label' => 'Therapist'],
                        ['value' => 'Parent',        'label' => 'Parent'],
                        ['value' => 'Student',       'label' => 'Student'],
                    ];
                @endphp

                @foreach ($roles as $r)
                    <button type="button"
                            class="role-btn {{ old('role') === $r['value'] ? 'active' : '' }}"
                            onclick="selectRole('{{ $r['value'] }}', this)">
                        {{ $r['label'] }}
                    </button>
                @endforeach

                <input type="hidden" name="role" id="roleInput" value="{{ old('role') }}">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="name@school.edu"
                       autocomplete="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg">Login</button>
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
        </form>
    </div>

    <p class="login-tagline">Helping students focus, one task at a time.</p>

    <script>
        function selectRole(value, btn) {
            document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('roleInput').value = value;
        }
    </script>

</body>
</html>
