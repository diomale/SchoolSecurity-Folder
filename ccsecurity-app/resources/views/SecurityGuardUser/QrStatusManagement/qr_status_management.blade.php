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

        <!-- Users Table -->
        <div>
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
                        <td colspan="6">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($inside_users->hasPages())
            <div>
                {{ $inside_users->links() }}
            </div>
            @endif
        </div>

        <!-- Stats Summary -->
        <div>
            <div>
                <div>Total Users</div>
                <div>{{ $inside_users->total() }}</div>
            </div>
            <div>
                <div>Active QR</div>
                <div>{{ $inside_users->filter(fn($u) => in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
            <div>
                <div>Inactive QR</div>
                <div>{{ $inside_users->filter(fn($u) => !in_array(strtolower($u->qr_status), ['active']))->count() }}</div>
            </div>
        </div>
    </div>
</body>
</html>
