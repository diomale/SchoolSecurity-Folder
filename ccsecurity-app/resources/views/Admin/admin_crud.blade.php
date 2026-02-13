<div>
    <h1>CRUD USER</h1>

    <a href="{{ route('admin.add.user') }}">Add+</a>

    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inside_users as $inside_user)
            <tr>
                <td>{{ $inside_user->first_name }}</td>
                <td>{{ $inside_user->last_name }}</td>
                <td>{{ $inside_user->email }}</td>
                <td>{{ $inside_user->role }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <a href="{{ route('admin.dashboard') }}">Back</a>
</div>
