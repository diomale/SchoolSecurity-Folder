<div>
    <h1>Edit Admin Section</h1>
    <div>
        <form action="{{ route('superadmin.admin.update', ['id' => $admin->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Name</label>
            <input type="text" name="name" value="{{ $admin->name }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ $admin->email }}" required>

            <label>Password</label>
            <input type="password" name="password" value="{{ $admin->password }}" required>

            <label for="status">Account Status</label>
            <select name="status" id="status">
                <option value="1" {{ old('status', $admin->status) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $admin->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>


            <button type="submit">Save Changes</button>
        </form>
    </div>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <a href="{{ route('superadmin.dashboard') }}">Back</a>
</div>
