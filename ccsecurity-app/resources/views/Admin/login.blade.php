<div>
    <h1>Admin Login</h1>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <label>Email: </label>
        <input type="email" name="email" placeholder="Email" autocomplete="off" required>
        <br>
        <label>Password: </label>
        <input type="password" name="password" placeholder="Password" autocomplete="off" required>

        <button type="submit">Login</button>

        @error('email')
            <p style="color:red">{{ $message }}</p>
        @enderror
    </form>

    <a href="{{ route('welcome') }}">Back</a>
</div>
