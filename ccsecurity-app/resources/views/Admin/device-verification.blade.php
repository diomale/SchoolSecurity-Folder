<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Device Verification - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
    <style>
        .verification-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #FFFFFF;
        }
        .verification-steps {
            margin: 24px 0;
            text-align: left;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 8px;
        }
        .step-number {
            width: 24px;
            height: 24px;
            background: #FFFFFF;
            color: #000000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .step-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        .step-text strong {
            color: #FFFFFF;
        }
        .resend-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 16px;
            transition: all 0.3s ease;
            background: none;
            border: none;
            cursor: pointer;
        }
        .resend-link:hover {
            color: #FFFFFF;
        }
        .info-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="welcome-container">
        <!-- Header -->
        <header class="welcome-header">
            <div class="logo-area">
                <a href="{{ route('welcome.page') }}" class="logo-circle logo-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </a>
                <h1 class="header-title">Columban College <span class="highlight">Security System</span></h1>
            </div>
        </header>

        <!-- Verification Content -->
        <main class="login-choice-main fade-in">
            <div class="login-choice-card glass-panel">
                <div class="login-choice-header">
                    <div class="verification-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <div class="info-badge">New Device Detected</div>
                    <h2>Verify Your Identity</h2>
                    <p class="text-muted">We sent a verification code to <strong>{{ $user->email }}</strong></p>
                </div>

                <div class="verification-steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-text">Check your email inbox for the verification code</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-text">Enter the <strong>6-digit code</strong> below</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-text">This device will be trusted for future logins</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.device.verify.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label>Verification Code</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input type="text" name="verification_code" class="form-control" required placeholder="Enter 6-digit code" maxlength="6" pattern="[0-9]{6}" autofocus style="text-align: center; letter-spacing: 8px; font-size: 1.2rem; font-weight: 700;">
                        </div>
                        @error('verification_code')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-success" style="border-left-color: #EF4444; color: #F87171;">{{ session('error') }}</div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-block">
                        Verify Device
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </form>

                <div class="login-choice-footer">
                    <form method="POST" action="{{ route('admin.device.verify.resend') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="resend-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                            Resend Code
                        </button>
                    </form>
                    <br>
                    <a href="{{ route('admin.login') }}" class="back-link-inline" style="margin-top: 12px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Back to Login
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="welcome-footer">
            <p>&copy; {{ date('Y') }} Columban College Security System</p>
            <div class="footer-links">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <span class="footer-divider">|</span>
                <a href="{{ route('terms') }}">Terms of Service</a>
            </div>
        </footer>
    </div>
</body>
</html>
