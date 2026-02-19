<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
    <h1>Waiting For Approval</h1>

    <table>
        <thead>
            <tr>
                <th>Full name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outside_users as $outside_user)
            <tr>
                <td>{{ $outside_user->first_name }} {{ $outside_user->last_name }}</td>
                <td>{{ $outside_user->email }}</td>
                <td>{{ $outside_user->created_at }}</td>
                <td>{{ $outside_user->updated_at }}</td>
                <td>{{ $outside_user->status }}</td>

                <td>
                    <form action="{{ route('admin.approved.user', $outside_user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit">Approved</button>
                    </form>
                </td>

                <td>
                    <form action="">
                        <button type="submit">Edit</button>
                    </form>
                </td>

                <td>
                    <form action="">
                        <button type="submit" onclick="return confirm('Are you sure?')">delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
