<div>
    <h1>Admin Login</h1>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <input type="email" name="email" placeholder="Email" autocomplete="off" required>
        <input type="password" name="password" placeholder="Password" autocomplete="off" required>

        <button type="submit">Login</button>

        @error('email')
            <p style="color:red">{{ $message }}</p>
        @enderror
    </form>
</div>
