<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Columban College Security System</title>
</head>
<body>
    <h1>Columban College Inc, Security System</h1>
    <hr>

    <div>
        <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->

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

    <div>
        <div>
            <p>Are you a Visitor? <a href="{{ route('outsideuser.login.show') }}">Click Here</a></p>
        </div>
    </div>
    
</body>
</html>