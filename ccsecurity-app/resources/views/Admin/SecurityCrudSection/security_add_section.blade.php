<div>
    <h1>Add User</h1>

    <form action="{{ route('security.add.accept') }}" method="POST">
        @csrf

        <input type="text" name="first_name" placeholder="First name: " required>
        <input type="text" name="last_name" placeholder="Last name: " required>
        <input type="email" name="email" placeholder="Email: " required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required required autocomplete="off">

        <button type="submit">Submit</button>

    </form>

    <a href="{{ route('security.user.table.section') }}">Back</a>