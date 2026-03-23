<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin Login - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_login.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <div class="login-container">
        <div class="glass-card">
            <div class="header">
                <div class="logo-circle" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem; margin: 0 auto 20px auto; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5);">CCSS</div>
                <h1>Super Admin Portal</h1>
                <p>Sign in to manage system administrators</p>
            </div>

            <form method="POST" action="{{ route('superadmin.login.submit') }}">
                @csrf

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" class="form-input" placeholder="Admin Email" value="{{ old('email') }}" autocomplete="off" required>
                        <span class="input-icon">✉️</span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Password" autocomplete="off" required>
                        <span class="input-icon">🔒</span>
                    </div>
                </div>

                <button type="submit" class="btn-custom">Secure Login</button>

                @error('email')
                    <div class="error-message">
                        <span>⚠️</span> {{ $message }}
                    </div>
                @enderror
                @error('password')
                    <div class="error-message">
                        <span>⚠️</span> {{ $message }}
                    </div>
                @enderror
            </form>

            <a href="{{ route('welcome') }}" class="back-link">&larr; Return to main portal</a>
        </div>
    </div>
</body>
</html>
