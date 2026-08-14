<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        var SITE_KEY = '{{ config('services.recaptcha.site_key') }}';

        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('login-btn');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof grecaptcha === 'undefined') {
                        alert('reCAPTCHA is still loading. Please try again in a moment.');
                        return;
                    }
                    grecaptcha.ready(function() {
                        grecaptcha.execute(SITE_KEY, {action: 'submit'}).then(function(token) {
                            document.getElementById('g-recaptcha-response').value = token;
                            document.getElementById('loginForm').submit();
                        });
                    });
                });
            }
        });
    </script>
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
        </header>

        <!-- Login Content -->
        <main class="login-choice-main fade-in">
            <div class="login-choice-card glass-panel">
                <div class="login-choice-header">
                    <div class="login-option-icon" style="margin: 0 auto 16px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h2>Admin Portal</h2>
                    <p class="text-muted">Manage the system</p>
                </div>

                <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" name="email" class="form-control" required placeholder="admin@example.com" value="{{ old('email') }}" autofocus>
                        </div>
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrap password-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror

                        @error('g-recaptcha-response')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <button type="submit" id="login-btn" class="btn btn-primary btn-block">
                        Secure Login
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>

                <div class="login-choice-footer">
                    <a href="{{ route('welcome.page') }}" class="back-link-inline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Return to main portal
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="welcome-footer">
            <p>&copy; {{ date('Y') }} KitaKits: Columban College Security System</p>
            <div class="footer-links">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <span class="footer-divider">|</span>
                <a href="{{ route('terms') }}">Terms of Service</a>
            </div>
        </footer>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.querySelector('.eye-open');
            const eyeClosed = document.querySelector('.eye-closed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
</body>
</html>
