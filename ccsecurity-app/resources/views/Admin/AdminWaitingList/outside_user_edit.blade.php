<div>
    <h1>Edit Visitor Account</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.outsider.update', $outside_user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" value="{{ old('first_name', $outside_user->first_name) }}" required>
        </div>
        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" value="{{ old('last_name', $outside_user->last_name) }}" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email', $outside_user->email) }}" required>
        </div>
        <div>
            <label>Phone Number:</label>
            <input type="text" name="phone_number" value="{{ old('phone_number', $outside_user->phone_number) }}">
        </div>
        <div>
            <label>Password (Leave blank to keep current):</label>
            <input type="password" name="password" autocomplete="off">
        </div>
        <div>
            <label>Purpose of Visit:</label>
            <input type="text" name="purpose_of_visit" value="{{ old('purpose_of_visit', $outside_user->purpose_of_visit) }}" required>
        </div>
        <div>
            <label>QR Status:</label>
            <select name="qr_status">
                <option value="active" {{ $outside_user->qr_status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $outside_user->qr_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit">Update Account</button>
    </form>

    <br>
    <a href="{{ route('show.admin.outsider.list') }}">Back to List</a>
</div>