<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration - School Security</title>
    @vite(['resources/css/OutsideUser/outsideuser_style_signup.css', 'resources/js/app.js'])
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        var SITE_KEY = '{{ config('services.recaptcha.site_key') }}';

        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('signup-btn');
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
                            document.getElementById('signup-form').submit();
                        });
                    });
                });
            }
        });
    </script>
</head>
<body>
    <div class="signup-card">

        <div class="signup-header">
            <div class="signup-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <h1>Visitor Registration</h1>
            <p>Parents & Guests</p>
        </div>

        @if(session('success'))
            <div class="signup-alert signup-alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="signup-alert signup-alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <div>
                    <strong>Please fix the errors:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('outsideuser.signup.request') }}" method="POST" id="signup-form">
            @csrf
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

            <div class="signup-form-row">
                <div class="signup-form-group">
                    <label>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        First Name
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required>
                </div>
                <div class="signup-form-group">
                    <label>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Last Name
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
                </div>
            </div>

            <div class="signup-form-group">
                <label>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
            </div>

            <div class="signup-form-group">
                <label>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Phone Number
                </label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="09xx xxx xxxx" required>
            </div>

            <div class="signup-form-row">
                <div class="signup-form-group">
                    <label>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Password
                    </label>
                    <input type="password" name="password" placeholder="Min 8 characters" required>
                </div>
                <div class="signup-form-group">
                    <label>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </div>
            </div>

            <div class="signup-form-group terms-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="agree_terms" value="1" required>
                    <span>I agree to the <a href="#" onclick="event.preventDefault(); openModal('privacy')">Privacy Policy</a> and <a href="#" onclick="event.preventDefault(); openModal('terms')">Terms of Service</a></span>
                </label>
            </div>

            <button type="submit" id="signup-btn" class="signup-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Create Account
            </button>
        </form>

        <div class="signup-footer">
            <p>Already have an account? <a href="{{ route('outsideuser.login.show') }}">Login here</a></p>
            <a href="{{ route('welcome.page') }}" class="signup-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Home
            </a>
        </div>

    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacy-modal" class="terms-modal-overlay" onclick="closeModal('privacy')">
        <div class="terms-modal" onclick="event.stopPropagation()">
            <div class="terms-modal-header">
                <h2>Privacy Policy</h2>
                <button class="terms-modal-close" onclick="closeModal('privacy')">&times;</button>
            </div>
            <div class="terms-modal-body">
                <iframe src="{{ route('privacy') }}" width="100%" height="100%" frameborder="0" title="Privacy Policy"></iframe>
            </div>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div id="terms-modal" class="terms-modal-overlay" onclick="closeModal('terms')">
        <div class="terms-modal" onclick="event.stopPropagation()">
            <div class="terms-modal-header">
                <h2>Terms of Service</h2>
                <button class="terms-modal-close" onclick="closeModal('terms')">&times;</button>
            </div>
            <div class="terms-modal-body">
                <iframe src="{{ route('terms') }}" width="100%" height="100%" frameborder="0" title="Terms of Service"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openModal(type) {
            document.getElementById(type + '-modal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(type) {
            document.getElementById(type + '-modal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('privacy');
                closeModal('terms');
            }
        });
    </script>
</body>
</html>
