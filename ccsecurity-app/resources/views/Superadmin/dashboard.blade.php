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
            </tr>
            @endforeach
        </tbody>
    </table>
</div>