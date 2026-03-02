<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration - School Security</title>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        // 2. The callback function called by the button
        function onSubmit(token) {
            document.getElementById("signup-form").submit();
        }
    </script>
</head>
<body>
    <div>
        <h1>Visitor Registration (Parents/Guests)</h1>
        
        @if(session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div style="color: red;">
                <strong>Please fix the errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('outsideuser.signup.request') }}" method="POST" id="signup-form">
            @csrf

            <div>
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            </div>

            <div>
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div>
                <label>Confirm Password *</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <button class="g-recaptcha" 
                    data-sitekey="{{ config('services.recaptcha.site_key') }}" 
                    data-callback='onSubmit' 
                    data-action='submit'>
                Submit Registration
            </button>
        </form>
    </div>
</body>
</html>