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

        <button type="submit">Login</button>
        
        @error('email')
            <p style="color:red">{{ $message }}</p>
        @enderror

        @if (session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
    </form>
</div>