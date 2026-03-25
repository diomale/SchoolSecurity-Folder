<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_login.css', 'resources/js/app.js'])
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
                <div class="logo-circle">CCSS</div>
                <h1>Admin Portal</h1>
                <p>Sign in to manage the system</p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" class="form-input" placeholder="Admin Email" autocomplete="off" required>
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Password" autocomplete="off" required>
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-custom">Login</button>

                @error('email')
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
