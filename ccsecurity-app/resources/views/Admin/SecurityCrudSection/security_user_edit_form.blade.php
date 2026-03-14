<div>
    <!-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh -->
    <div>
        <h1>Edit Security</h1>

        
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('security.guard.user.update', $security_guard_user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label>First Name:</label><br>
                <input type="text" name="first_name" value="{{ old('first_name', $security_guard_user->first_name) }}" required>
                @error('first_name') <small style="color: red;">{{ $message }}</small> @enderror
            </div>

            <br>

            <div>
                <label>Last Name:</label><br>
                <input type="text" name="last_name" value="{{ old('last_name', $security_guard_user->last_name) }}" required>
                @error('last_name') <small style="color: red;">{{ $message }}</small> @enderror
            </div>

            <br>

            <div>
                <label>Email Address:</label><br>
                <input type="email" name="email" value="{{ old('email', $security_guard_user->email) }}" required>
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
                    <option value="1" {{ old('status', $security_guard_user->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $security_guard_user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <br>

            <button type="submit">Update User</button>
            <a href="{{ $backUrl }}">Cancel</a>
        </form>
    </div>
</div>
