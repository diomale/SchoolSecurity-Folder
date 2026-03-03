<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Login - School Security</title>
</head>
<body>
    <div>
        <h1>Visitor Login</h1>
        <p>Login to manage your visit requests and QR code</p>

        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div>
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('outsideuser.login.submit') }}">
            @csrf

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            @error('email')
                <div>{{ $message }}</div>
            @enderror

            <button type="submit">Login</button>
        </form>

        <div>
            <p>Don't have an account? <a href="{{ route('outsideuser.signup.show') }}">Register here</a></p>
            <p><a href="{{ route('welcome') }}">Back to Home</a></p>
        </div>
    </div>
</body>
</html>
