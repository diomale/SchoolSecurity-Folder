<div>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h1>Login</h1>
    <h2>Security Guard Portal</h2>

    <form method="POST" action="{{ route('security.login.submit') }}">
        @csrf

        <div>
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email">
            @error('email')
                <span style="color:red; display:block;">{{ $message }}</span>
            @enderror
        </div>

        <br>

        <div>
            <label for="password">Password: </label>
            <input type="password" name="password" id="password" required placeholder="password">
            @error('password')
                <span style="color:red; display:block;">{{ $message }}</span>
            @enderror
        </div>

        <br>

        <button type="submit">Login</button>
    </form>
</div>