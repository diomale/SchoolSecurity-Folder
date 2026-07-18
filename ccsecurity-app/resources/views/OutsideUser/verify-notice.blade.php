<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - CCSS</title>
    @vite(['resources/css/OutsideUser/outsideuser_style_login.css','resources/js/app.js'])
</head>
<body>
    <div class="login-card">

        <div class="login-header">
            <div class="login-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h1>Check Your Email</h1>
            <p>We sent a verification link to your email address</p>
        </div>

        @if(session('success'))
            <div class="login-alert login-alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="login-alert login-alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div style="text-align: center; margin-bottom: 28px;">
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 20px; margin-bottom: 20px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #94a3b8; margin-bottom: 12px;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 8h10"/><path d="M7 12h6"/></svg>
                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                    Click the link in the email to verify your account.<br>
                    The link expires in <strong style="color: #e2e8f0;">24 hours</strong>.
                </p>
            </div>
        </div>

        <div style="text-align: center;">
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 16px;">Didn't receive the email?</p>
            <form action="{{ route('outsideuser.verify.resend') }}" method="POST" style="margin-bottom: 20px;">
                @csrf
                <button type="submit" class="login-btn" style="font-size: 0.95rem; padding: 14px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Resend Verification Email
                </button>
            </form>
        </div>

        <div class="login-footer">
            <a href="{{ route('outsideuser.login.show') }}" class="login-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Login
            </a>
        </div>

    </div>
</body>
</html>
