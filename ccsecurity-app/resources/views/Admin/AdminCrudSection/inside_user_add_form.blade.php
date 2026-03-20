
@vite(['resources/css/AdminStyleFolder/insideuser_style_add_form.css', 'resources/js/app.js'])
<div>
    <h1>Add User</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.add.user.accept') }}" method="POST">
        @csrf

        <input type="text" name="first_name" placeholder="First name: " value="{{ old('first_name') }}" required>
        <input type="text" name="last_name" placeholder="Last name: " value="{{ old('last_name') }}" required>
        <input type="email" name="email" placeholder="Email: " value="{{ old('email') }}" required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required autocomplete="off">
        
        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="security_guard">Security Guard</option>
        </select>

        <label for="qr_status">QR Status:</label>
        <select name="qr_status" id="qr_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <br>
    <a href="{{ route('admin.show.crudSection') }}">Back</a>
</div>