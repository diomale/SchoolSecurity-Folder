<div>
    <h1>Super Admin Dashboard</h1>

    <p>Welcome, {{ auth('superadmin')->user()->name }}</p>

    <form method="POST" action="{{ route('superadmin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($admins as $admin)
            <tr>
                <td> {{ $admin->name }} </td>
                <td> {{ $admin->email }} </td>
                <td>
                    <form action="{{ route('superadmin.admin.show', $admin->id  ) }}">
                        <button type="submit">View</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('superadmin.admin.edit', $admin->id ) }} ">
                        <button type="submit">Edit</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('superadmin.admin.delete', $admin->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>