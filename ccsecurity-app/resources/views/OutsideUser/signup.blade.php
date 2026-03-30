<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration - School Security</title>
    
    @vite(['resources/css/OutsideUser/outsideuser_style_signup.css', 'resources/js/app.js'])

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        // 2. The callback function called by the button
        function onSubmit(token) {
            document.getElementById("signup-form").submit();
        }
    </script>
</head>
<body>
    <div class="registration-container">
        <h1>Visitor Registration</h1>
        <p class="subtitle">Parents & Guests</p>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
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

            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button class="g-recaptcha" 
                    data-sitekey="{{ config('services.recaptcha.site_key') }}" 
                    data-callback='onSubmit' 
                    data-action='submit'>
                Submit Registration
            </button>
            <div class="form-footer">
                <p><a href="{{ route('welcome') }}">Back to Home</a></p>
            </div>
        </form>
    </div>
</body>
</html>
