<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Status Management - Security Guard</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>QR Status Management</h1>
            <a href="{{ route('security.dashboard') }}">← Back to Dashboard</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        <!-- Search -->
        <div>
            <form method="GET" action="{{ route('security.qr.status.management') }}">
                <input
                    type="text"
                    name="search"
                    placeholder="Search by ID, Name, Email, or QR Value..."
                    value="{{ request('search') }}"
                >
                <button type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('security.qr.status.management') }}">Clear</a>
                @endif
            </form>
        </div>

        <!-- Inside Users Table (Staff/Students) -->
        <div>
            <h2>Staff/Students QR Status</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>QR Value</th>
                        <th>QR Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inside_users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->fullname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->qr_value }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                ✓ Active
                            @else
                                ✗ Inactive
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('security.qr.status.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                @if(in_array(strtolower($user->qr_status), ['active']))
                                    <button type="submit" onclick="return confirm('Deactivate QR for {{ $user->fullname }}?')">
                                        Deactivate
                                    </button>
                                @else
                                    <button type="submit" onclick="return confirm('Activate QR for {{ $user->fullname }}?')">
                                        Activate
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">No staff/student users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Inside Users -->
            @if($inside_users->hasPages())
            <div>
                {{ $inside_users->links() }}
            </div>
            @endif
        </div>

        <hr style="margin: 2rem 0;">

        <!-- Outside Users Table (Visitors) -->
        <div>
            <h2>Visitors QR Status</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>QR Value</th>
                        <th>QR Status</th>
                        <th>Account Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outside_users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->fullname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                        <td>{{ $user->qr_value }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                ✓ Active
                            @else
                                ✗ Inactive
                            @endif
                        </td>
                        <td>
                            @if($user->status == 1)
                                ✓ Approved
                            @elseif($user->status == 2)
                                ✗ Rejected
                            @else
                                ⏳ Pending
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('security.qr.status.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                @if(in_array(strtolower($user->qr_status), ['active']))
                                    <button type="submit" onclick="return confirm('Deactivate QR for {{ $user->fullname }}?')">
                                        Deactivate
                                    </button>
                                @else
                                    <button type="submit" onclick="return confirm('Activate QR for {{ $user->fullname }}?')">
                                        Activate
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">No visitor users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Outside Users -->
            @if($outside_users->hasPages())
            <div>
                {{ $outside_users->links() }}
            </div>
            @endif
        </div>

        <!-- Stats Summary -->
        <div>
            <h3>Statistics</h3>
            <div>
                <div>Staff/Students Total</div>
                <div>{{ $inside_users->total() }}</div>
            </div>
            <div>
                <div>Staff/Students Active QR</div>
                <div>{{ $inside_users->filter(fn($u) => in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
            <div>
                <div>Staff/Students Inactive QR</div>
                <div>{{ $inside_users->filter(fn($u) => !in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
            <hr>
            <div>
                <div>Visitors Total</div>
                <div>{{ $outside_users->total() }}</div>
            </div>
            <div>
                <div>Visitors Active QR</div>
                <div>{{ $outside_users->filter(fn($u) => in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
            <div>
                <div>Visitors Inactive QR</div>
                <div>{{ $outside_users->filter(fn($u) => !in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
        </div>
    </div>
</body>
</html>
