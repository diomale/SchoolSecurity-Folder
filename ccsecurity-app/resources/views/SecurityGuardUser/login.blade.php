<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Login - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_login.css'])
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-wrapper">
        <div class="login-glass-card">
            
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="brand-header">
                <div class="logo-circle">CCSS</div>
                <h1>Security Guard</h1>
                <p>Command Portal Login</p>
            </div>

            <form method="POST" action="{{ route('security.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="Enter your email" autofocus>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <a href="{{ route('welcome') }}" class="back-link">&larr; Back to Home</a>
        </div>
    </div>
</body>
</html>