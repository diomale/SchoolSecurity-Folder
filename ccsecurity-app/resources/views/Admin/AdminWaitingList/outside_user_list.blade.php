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

                <td>
                    @if($outside_user->status === \App\Models\OutsideUser::STATUS_PENDING)
                        <form action="{{ route('admin.approved.user', $outside_user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Approve</button>
                        </form>

                        <form action="{{ route('admin.rejected.user', $outside_user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Reject</button>
                        </form>
                    @else
                        {{ $outside_user->status == 1 ? 'Approved' : 'Rejected' }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.dashboard') }}">Back</a>
</div>
