<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Log in as a Visitor</h1>
    <div>
    <form method="POST" action="{{ route('outsideuser.login.submit') }}">
        @csrf

        <div>
            <label for="email">Email: </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="email">
        </div>

        <div>
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" required placeholder="password">
        </div>

        @error('email')
            <p style="color:red">{{ $message }}</p>
        @enderror

        @if (session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        <button type="submit">Login</button>
        
    </form>

    <p>Are you a Student or Staff ? <a href="{{ route('user.login.show') }}">Click Here</a></p>
    
    <a href="{{ route('outsideuser.signup.show') }}">Create New Account</a>
    <br>
    <a href="{{ route('welcome') }}">Back</a>
</div>
</body>
</html>