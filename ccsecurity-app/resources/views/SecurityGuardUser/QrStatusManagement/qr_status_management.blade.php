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
        <div style="color: green; margin: 10px 0;">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search -->
        <div style="margin-bottom: 20px;">
            <form method="GET" action="{{ route('security.qr.status.management') }}">
                <input
                    type="text"
                    name="search"
                    placeholder="Search by ID, Name, Email, or QR Value..."
                    value="{{ request('search') }}"
                    style="width: 300px;"
                >
                <button type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('security.qr.status.management') }}">Clear</a>
                @endif
            </form>
        </div>

        <!-- Students Table -->
        <div style="margin-top: 30px;">
            <h2>Students</h2>
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>QR Value</th>
                        <th>QR Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->qr_value }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                <span style="color: green;">✓ Active</span>
                            @else
                                <span style="color: gray;">✗ Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside']) }}" style="padding: 5px 10px; background-color: #e7f3ff; color: #007bff; text-decoration: none; border-radius: 4px; border: 1px solid #007bff;">View QR</a>
                                <form action="{{ route('security.qr.status.toggle', $user->id) }}" method="POST" style="display:inline;">
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Students -->
            @if($students->hasPages())
            <div style="margin-top: 20px;">
                {{ $students->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- Staff Table -->
        <div style="margin-top: 50px;">
            <h2>Staff</h2>
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>QR Value</th>
                        <th>QR Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->qr_value }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                <span style="color: green;">✓ Active</span>
                            @else
                                <span style="color: gray;">✗ Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'inside']) }}" style="padding: 5px 10px; background-color: #e7f3ff; color: #007bff; text-decoration: none; border-radius: 4px; border: 1px solid #007bff;">View QR</a>
                                <form action="{{ route('security.qr.status.toggle', $user->id) }}" method="POST" style="display:inline;">
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No staff members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Staff -->
            @if($staff->hasPages())
            <div style="margin-top: 20px;">
                {{ $staff->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <hr style="margin: 3rem 0;">

        <!-- Outside Users Table (Visitors) -->
        <div>
            <h2>Visitors</h2>
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
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
                        <td>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                        <td>{{ $user->qr_value }}</td>
                        <td>
                            @if(in_array(strtolower($user->qr_status), ['active']))
                                <span style="color: green;">✓ Active</span>
                            @else
                                <span style="color: gray;">✗ Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status == \App\Models\OutsideUser::STATUS_APPROVED)
                                <span style="color: green;">✓ Approved</span>
                            @elseif($user->status == \App\Models\OutsideUser::STATUS_REJECTED)
                                <span style="color: red;">✗ Rejected</span>
                            @else
                                <span style="color: orange;">⏳ Pending</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('security.user.qr', ['id' => $user->id, 'type' => 'outside']) }}" style="padding: 5px 10px; background-color: #e7f3ff; color: #007bff; text-decoration: none; border-radius: 4px; border: 1px solid #007bff;">View QR</a>
                                <form action="{{ route('security.qr.status.toggle', $user->id) }}" method="POST" style="display:inline;">
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">No visitor users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination for Outside Users -->
            @if($outside_users->hasPages())
            <div style="margin-top: 20px;">
                {{ $outside_users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- Stats Summary -->
        <div style="margin-top: 30px; background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3>Summary Statistics</h3>
            <div style="display: flex; gap: 40px;">
                <div>
                    <strong>Total Students:</strong> {{ $students->total() }}
                </div>
                <div>
                    <strong>Total Staff:</strong> {{ $staff->total() }}
                </div>
                <div>
                    <strong>Total Visitors:</strong> {{ $outside_users->total() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>