<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->

    <h1>Edit Form</h1>
    <div>
        <form action="{{ route('admin.update.user', ['id' => $inside_user->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <label>First name: </label>
            <input type="text" name="first_name" value="{{ $inside_user->first_name }}" required>

            <label>Last name: </label>
            <input type="text" name="last_name" value="{{ $inside_user->last_name }}" required>
            
            <label>Email: </label>
            <input type="email" name="email" value="{{ $inside_user->email }}" required>

            <label>Password: </label>
            <input type="password" name="password" >
            
            <label for="status">Account Status</label>
            <select name="status" id="status">
                <option value="1" {{ old('status', $inside_user->status) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $inside_user->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit">Update</button>
        </form>
    </div>

    <a href="{{ route('admin.show.crudSection') }}">Back</a>
</div>
