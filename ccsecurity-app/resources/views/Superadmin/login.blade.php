<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="{{ route('superadmin.login.submit') }}">
    @csrf

    <input type="email" name="email" placeholder="Email" autocomplete="off" required>
    <input type="password" name="password" placeholder="Password" autocomplete="off" required>

    <button type="submit">Login</button>

    @error('email')
        <p style="color:red">{{ $message }}</p>
    @enderror
</form>

</body>
</html>