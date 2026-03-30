<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Staff Login - CCSS</title>
    @vite(['resources/css/InsideUser/insideuser_style_login.css'])
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-wrapper">
        <div class="login-glass-card">
            <div class="brand-header">
                <div class="logo-circle">CCSS</div>
                <h1>Student Staff Login</h1>
                <p>Login to your account</p>
            </div>

            <form method="POST" action="{{ route('insideuser.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email" autofocus>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <a href="{{ route('welcome') }}" class="back-link">&larr; Back to Home</a>
        </div>
    </div>
</body>
</html>
