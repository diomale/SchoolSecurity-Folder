<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
    <h1>Visitor Accounts - Approval List</h1>

    <div>
        <p><a href="{{ route('admin.visit.requests') }}">View Visit Requests</a></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>QR Value</th>
                <th>QR Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outside_users as $outside_user)
            <tr>
                <td>{{ $outside_user->id }}</td>
                <td>{{ $outside_user->fullname ?? ($outside_user->first_name . ' ' . $outside_user->last_name) }}</td>
                <td>{{ $outside_user->email }}</td>
                <td>{{ $outside_user->phone_number ?? 'N/A' }}</td>
                <td>{{ $outside_user->qr_value ?? 'N/A' }}</td>
                <td>
                    @if($outside_user->qr_status === 'active')
                        Active ✓
                    @else
                        Inactive ✗
                    @endif
                </td>
                <td>{{ $outside_user->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>

                <td>
                    @if($outside_user->status === \App\Models\OutsideUser::STATUS_PENDING)
                        <form action="{{ route('admin.approved.user', $outside_user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Approve Account</button>
                        </form>

                        <form action="{{ route('admin.rejected.user', $outside_user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Reject</button>
                        </form>
                    @elseif($outside_user->status === \App\Models\OutsideUser::STATUS_APPROVED)
                        <span>✓ Approved</span>
                    @else
                        <span>✗ Rejected</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
</div>
