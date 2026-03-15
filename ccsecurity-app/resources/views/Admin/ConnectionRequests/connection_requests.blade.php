<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent-Child Connection Requests - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            color: white;
        }
        .stat-pending { background: #ff9800; }
        .stat-approved { background: #4caf50; }
        .stat-rejected { background: #f44336; }
        .stat-card h3 { margin: 0; font-size: 28px; }
        .stat-card p { margin: 5px 0 0; opacity: 0.9; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back {
            background: #6c757d;
            color: white;
        }
        .btn-back:hover { background: #5a6268; }
        .alert {
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .pagination {
            display: flex;
            gap: 5px;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination .active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Parent-Child Connection Requests</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">← Back to Dashboard</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
             {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
             {{ session('error') }}
        </div>
        @endif

        <!-- Info Box -->
        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <strong>ℹ Information:</strong>
            <p style="margin: 10px 0 0 0;">Parent-child connections now only require <strong>student approval</strong>. Admin approval is no longer needed. When a student accepts a connection request, it's automatically approved.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats">
            <div class="stat-card stat-pending">
                <h3>{{ $pendingCount }}</h3>
                <p>Pending Student Approval</p>
            </div>
            <div class="stat-card stat-approved">
                <h3>{{ $approvedCount }}</h3>
                <p>Approved (Auto)</p>
            </div>
            <div class="stat-card stat-rejected">
                <h3>{{ $rejectedCount }}</h3>
                <p>Rejected</p>
            </div>
        </div>

        <!-- Connection Requests Table -->
        <table>
            <thead>
                <tr>
                    <th>Parent/Visitor</th>
                    <th>Student/Child</th>
                    <th>Relationship</th>
                    <th>Student Approval</th>
                    <th>Status</th>
                    <th>Requested On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($connectionRequests as $connection)
                <tr>
                    <td>
                        <strong>{{ $connection->outsideUser->fullname ?? 'N/A' }}</strong><br>
                        <small>{{ $connection->outsideUser->email ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <strong>{{ $connection->insideUser->fullname ?? 'N/A' }}</strong><br>
                        <small>{{ $connection->insideUser->email ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $connection->relationship }}</td>
                    <td>
                        <span class="status-badge status-{{ $connection->inside_user_approval }}">
                            @if($connection->inside_user_approval === 'accepted')
                                 Accepted
                            @elseif($connection->inside_user_approval === 'rejected')
                                 Rejected
                            @else
                                 Pending
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $connection->status }}">
                            @if($connection->status === 'approved')
                                 Approved
                            @elseif($connection->status === 'rejected')
                                 Rejected
                            @else
                                 Pending
                            @endif
                        </span>
                    </td>
                    <td>{{ $connection->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                        No connection requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($connectionRequests->hasPages())
        <div class="pagination">
            {{ $connectionRequests->links() }}
        </div>
        @endif
    </div>

    <script>
    </script>
</body>
</html>
