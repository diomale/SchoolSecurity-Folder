<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Columban College Security System</title>
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
</head>
<body>
    <h1>Columban College Inc, Security System</h1>
    <hr>

    <!-- MAIN FLEX CONTAINER -->
    <div class="main-content">

        <!-- LEFT PANEL: Login + Cards -->
        <div class="left-panel">
            <!-- Login Form -->
            <div class="login-container">
                <h2>Log in to CCSS</h2>

                <form method="POST" action="{{ route('insideuser.login.submit') }}">
                    @csrf

                    <label>Email: </label>
                    <input type="email" name="email" required placeholder="email">

                    <br>

                    <label>Password: </label>
                    <input type="password" name="password" required placeholder="password">

                    <br>
                    @error('email')
                        <p style="color:red">{{ $message }}</p>
                    @enderror

                    @if (session('success'))
                        <p style="color:green">{{ session('success') }}</p>
                    @endif

                    <button type="submit">Login</button>
                    <br>
                </form>
            </div>

            <!-- Visitor + Student-Staff Cards -->
            <div class="bottom-cards">
                <div class="visitors-card">
                    <h3>Visitor Registration (Parents/Guests)</h3>
                    <p>Register to request visits and get QR code access</p>
                    <a href="{{ route('outsideuser.signup.show') }}">Register as Visitor</a> | 
                    <a href="{{ route('outsideuser.login.show') }}">Visitor Login</a>
                </div>

                <div class="student-staff-card">
                    <p>Are you a Student or Staff? <a href="{{ route('user.login.show') }}">Login Here</a></p>
                    <p>Are you a Visitor? <a href="{{ route('outsideuser.login.show') }}">Login Here</a></p>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Reserved for Image -->
        <div class="right-panel">
            <img src="" alt="Columban Logo">
        </div>

    </div>
</body>
</html>