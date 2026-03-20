<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/InsideUserStyleFolder/insideuser_style_login.css','resources/js/app.js'])
    <title>Login</title>
</head>
<body>
    <div class="wrapper">
        <h1>Login as a Authorized User</h1>

        <div class="login-container">
            <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->

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
                <button type="submit">Login</button>
            </form>

            <a href="{{ route('user.login.show') }}"></a>
            <a href="{{ route('welcome') }}">Back</a>
        </div>
    </div>
</body>
</html>