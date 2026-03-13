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
        .btn-approve {
            background: #4caf50;
            color: white;
        }
        .btn-approve:hover { background: #45a049; }
        .btn-reject {
            background: #f44336;
            color: white;
        }
        .btn-reject:hover { background: #da322a; }
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
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 500px;
            margin: 100px auto;
        }
        .modal-content h3 {
            margin-top: 0;
            color: #f44336;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 80px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
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
            <h1>🔗 Parent-Child Connection Requests</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-back">← Back to Dashboard</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            ✗ {{ session('error') }}
        </div>
        @endif

        <!-- Info Box -->
        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <strong>ℹ️ Information:</strong>
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
                    <th>ID</th>
                    <th>Parent/Visitor</th>
                    <th>Student/Child</th>
                    <th>Relationship</th>
                    <th>Student Approval</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Admin Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($connectionRequests as $connection)
                <tr>
                    <td>#{{ $connection->id }}</td>
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
                                ✓ Accepted
                            @elseif($connection->inside_user_approval === 'rejected')
                                ✗ Rejected
                            @else
                                ⏳ Pending
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $connection->status }}">
                            @if($connection->status === 'approved')
                                ✓ Approved
                            @elseif($connection->status === 'rejected')
                                ✗ Rejected
                            @else
                                ⏳ Pending
                            @endif
                        </span>
                    </td>
                    <td>{{ $connection->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($connection->admin_remarks)
                            <small>{{ Str::limit($connection->admin_remarks, 30) }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($connection->status === 'pending')
                            @if($connection->inside_user_approval === 'accepted')
                                <span style="color: #4caf50; font-size: 12px;">✓ Auto-approved</span>
                            @elseif($connection->inside_user_approval === 'rejected')
                                <span style="color: #f44336; font-size: 12px;">Student rejected</span>
                            @else
                                <span style="color: #ff9800; font-size: 12px;">Waiting for student</span>
                            @endif

                            @if($connection->inside_user_approval !== 'rejected')
                                <button class="btn btn-reject" onclick="openRejectModal({{ $connection->id }})" style="opacity: 0.5; cursor: not-allowed;" disabled>✗ Reject</button>

                                <!-- Reject Modal -->
                                <div id="rejectModal{{ $connection->id }}" class="modal">
                                    <div class="modal-content">
                                        <h3>Reject Connection Request</h3>
                                        <p>Admin rejection is disabled. Only students can accept/reject their own connection requests.</p>
                                        <div class="modal-actions">
                                            <button type="button" class="btn btn-back" onclick="closeRejectModal({{ $connection->id }})">Close</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <span style="color: #999;">{{ ucfirst($connection->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
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
        function openRejectModal(id) {
            document.getElementById('rejectModal' + id).style.display = 'block';
        }

        function closeRejectModal(id) {
            document.getElementById('rejectModal' + id).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
