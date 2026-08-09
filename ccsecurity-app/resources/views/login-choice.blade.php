<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - KitaKits: Columban College Security System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
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

        <!-- Login Choice Content -->
        <main class="login-choice-main fade-in">
            <div class="login-choice-card glass-panel">
                <div class="login-choice-header">
                    <h2>Choose Your Login</h2>
                    <p class="text-muted">Select a portal</p>
                </div>

                <div class="login-options">
                    <a href="{{ route('user.login.show') }}" class="login-option-card">
                        <div class="login-option-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        </div>
                        <h3>Students & Staff</h3>
                        <p>Internal portal for students and staff</p>
                        <span class="login-option-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </a>

                    <a href="{{ route('outsideuser.login.show') }}" class="login-option-card">
                        <div class="login-option-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h3>Guest / Visitor</h3>
                        <p>Visit requests, connections, and QR passes</p>
                        <span class="login-option-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
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
</body>
</html>
