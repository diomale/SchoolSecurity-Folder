<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - Outside User</title>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

<div class="signup-container">

<h2>Create Account</h2>
<p>Please fill in the details below to request access.</p>

@if ($errors->any())
<div>
    <strong>Please fix the following errors:</strong>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('outsideuser.signup.request') }}" method="POST" id="signup-form">
@csrf

<label>First Name</label>
<input type="text" name="first_name" value="{{ old('first_name') }}" required>

<label>Last Name</label>
<input type="text" name="last_name" value="{{ old('last_name') }}" required>

<label>Email</label>
<input type="email" name="email" value="{{ old('email') }}" required>

<label>Phone Number</label>
<input type="text" name="phone_number" value="{{ old('phone_number') }}" required>

<label>Password</label>
<input type="password" name="password" required>

<label>Confirm Password</label>
<input type="password" name="password_confirmation" required>

<br><br>

<button
    class="g-recaptcha"
    data-sitekey="{{ config('services.recaptcha.site_key') }}"
    data-callback="onSubmit"
    data-action="submit">
    Register Account
</button>

@error('captcha')
<p>{{ $message }}</p>
@enderror

</form>

</div>

    <a href="{{ route('welcome') }}">Back</a>

<script>
function onSubmit(token) {
    document.getElementById("signup-form").submit();
}
</script>

</body>
</html>
