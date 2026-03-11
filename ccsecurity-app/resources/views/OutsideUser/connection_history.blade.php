<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection History - School Security</title>
    <style>
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }
        .status-approved {
            color: #4caf50;
            font-weight: bold;
        }
        .status-rejected {
            color: #f44336;
            font-weight: bold;
        }
        .connection-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>Connection History</h1>
            <p>View all your child connection requests</p>
            <a href="{{ route('outsider.dashboard') }}">← Back to Dashboard</a> | 
            <a href="{{ route('outsideuser.connections.request') }}">Request New Connection</a>
        </div>

        @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 15px 0;">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($connections->count() > 0)
        <div class="connection-card">
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Relationship</th>
                        <th>Status</th>
                        <th>Admin Remarks</th>
                        <th>Requested On</th>
                        <th>{{ ucfirst($connections->first()->status === 'approved' ? 'Approved' : 'Updated') }} On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($connections as $connection)
                    <tr>
                        <td>{{ $connection->insideUser->fullname ?? 'N/A' }}</td>
                        <td>{{ $connection->insideUser->email ?? 'N/A' }}</td>
                        <td>{{ $connection->relationship }}</td>
                        <td class="status-{{ $connection->status }}">
                            @if($connection->status === 'approved')
                                ✓ APPROVED
                            @elseif($connection->status === 'rejected')
                                ✗ REJECTED
                            @else
                                ⏳ PENDING
                            @endif
                        </td>
                        <td>
                            @if($connection->admin_remarks)
                                {{ $connection->admin_remarks }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $connection->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($connection->approved_at)
                                {{ $connection->approved_at->format('M d, Y h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div style="margin-top: 20px;">
                {{ $connections->links() }}
            </div>
        </div>
        @else
        <div class="connection-card">
            <p style="text-align: center; color: #666; padding: 40px;">
                No connection requests found.<br>
                <a href="{{ route('outsideuser.connections.request') }}">Request your first connection</a>
            </p>
        </div>
        @endif
    </div>
</body>
</html>
