<div>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->

    <h1>Add User</h1>

    <form action="{{ route('admin.add.user.accept') }}" method="POST">
        @csrf

        <input type="text" name="first_name" placeholder="First name: " required>
        <input type="text" name="last_name" placeholder="Last name: " required>
        <input type="email" name="email" placeholder="Email: " required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required required autocomplete="off">
        <select name="role" id="role">
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="security_guard">Security Guard</option>
        </select>

        <button type="submit">Submit</button>

    </form>

    <a href="{{ route('admin.show.crudSection') }}">Back</a>
</div>
