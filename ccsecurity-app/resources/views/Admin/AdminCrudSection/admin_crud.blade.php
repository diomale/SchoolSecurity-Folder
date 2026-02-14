<div>
    <h1>CRUD USER</h1>

    <a href="{{ route('admin.add.user') }}">Add+</a>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inside_users as $inside_user)
            <tr>
                <td>{{ $inside_user->fullname }}</td>
                <td>{{ $inside_user->email }}</td>
                <td>{{ $inside_user->role }}</td>
                <td>{{ $inside_user->created_at }}</td>
                <td>{{ $inside_user->updated_at }}</td>

                <td>
                    <form action="{{ route('admin.user.details', $inside_user->id) }}">
                        <button type="submit">View</button>
                    </form>
                </td>

                <td>
                    <form action="{{ route('admin.user.edit.form', $inside_user->id) }}">
                        <button type="submit">Edit</button>
                    </form>
                </td>

                <td>
                    <form action="{{ route('admin.user.delete', $inside_user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')">delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <a href="{{ route('admin.dashboard') }}">Back</a>
</div>
