<div>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->

    <a href="{{ route('security.user.add.section') }}">Add+</a>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($security_guard_users as $security_guard_user)
            <tr>
                <td>{{ $security_guard_user->first_name }}</td>
                <td>{{ $security_guard_user->email }}</td>
                <td>{{ $security_guard_user->created_at }}</td>
                <td>{{ $security_guard_user->updated_at }}</td>

                <td>
                    <form action="{{ route('security.guard.user.details', $security_guard_user->id) }}">
                        <button type="submit">View</button>
                    </form>
                </td>

                <td>
                    <form action="">
                        <button type="submit">Edit</button>
                    </form>
                </td>

                <td>
                    <form action="" method="POST">
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
