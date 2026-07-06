
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TekioTask</title>
    <link rel="stylesheet" href="{{ asset('css/tekiotask.css') }}">
    <link rel="manifest" href="/manifest.json">
</head>
<body class="login-body">

    <div class="login-brand">
        <div class="brand-icon">T</div>
        <span class="brand-name">TekioTask</span>
    </div>

    <div class="login-card">
        <h2>Forgot Password</h2>
        <p style="margin-bottom:20px; color:#6b7280;">
            Password resets are managed by the centre administrator.
        </p>

        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px;
                    padding:18px 20px; font-size:14px; color:#1e40af; margin-bottom:20px;
                    line-height:1.6;">
            Please contact your administrator at Smart Integrated Therapy Centre and
            ask them to reset your password through the staff management panel.
            They can set a new password for your account directly.
        </div>

        <a href="{{ route('login') }}" class="btn btn-primary btn-full">
            Back to Login
        </a>
    </div>

</body>
</html>
