<div>
    <h1>Edit Admin</h1>

    {{-- Global Error List (Good for debugging until frontend team styles it) --}}
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.update.user', $inside_user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>First Name:</label><br>
            <input type="text" name="first_name" value="{{ old('first_name', $inside_user->first_name) }}" required>
            @error('first_name') <small style="color: red;">{{ $message }}</small> @enderror
        </div>

        <br>

        <div>
            <label>Last Name:</label><br>
            <input type="text" name="last_name" value="{{ old('last_name', $inside_user->last_name) }}" required>
            @error('last_name') <small style="color: red;">{{ $message }}</small> @enderror
        </div>

        <br>

        <div>
            <label>Email Address:</label><br>
            <input type="email" name="email" value="{{ old('email', $inside_user->email) }}" required>
            @error('email') <small style="color: red;">{{ $message }}</small> @enderror
        </div>

        <br>

        <div>
            <label>Password (Leave blank to keep current):</label><br>
            <input type="password" name="password">
            @error('password') <small style="color: red;">{{ $message }}</small> @enderror
        </div>

        <br>

        <div>
            <label for="status">Account Status:</label><br>
            <select name="status" id="status">
                <option value="1" {{ old('status', $inside_user->status) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $inside_user->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <br>

        <button type="submit">Update User</button>
        <a href="{{ route('admin.show.crudSection') }}">Cancel</a>
    </form>
</div>