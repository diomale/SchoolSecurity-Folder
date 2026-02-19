<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->

    <h1>Login</h1>

    <form method="POST" action="{{ route('insideuser.login.submit') }}">
        @csrf

        <label>Email: </label>
        <input type="email" name="email" required placeholder="email">

        <label>Email: </label>
        <input type="password" name="password" required placeholder="password">

        <button type="submit">Login</button>
        
        @error('email')
            <p style="color:red">{{ $message }}</p>
        @enderror
    </form>

</div>
