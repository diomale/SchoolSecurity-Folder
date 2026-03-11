<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Requests - Inside User</title>
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
        .stat-accepted { background: #4caf50; }
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
        .status-accepted {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-approved {
            background: #cce5ff;
            color: #004085;
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
        .btn-accept {
            background: #4caf50;
            color: white;
        }
        .btn-accept:hover { background: #45a049; }
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
        .btn-view {
            background: #2196f3;
            color: white;
        }
        .btn-view:hover { background: #1976d2; }
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
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Parent Connection Requests</h1>
            <div class="nav-links">
                <a href="{{ route('insideuser.dashboard') }}">← Back to Dashboard</a>
                <a href="{{ route('insideuser.connected.parents') }}">My Connected Parents</a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats">
            <div class="stat-card stat-pending">
                <h3>{{ $pendingCount }}</h3>
                <p>Pending Your Approval</p>
            </div>
            <div class="stat-card stat-accepted">
                <h3>{{ $acceptedCount }}</h3>
                <p>Accepted by You</p>
            </div>
            <div class="stat-card stat-rejected">
                <h3>{{ $rejectedCount }}</h3>
                <p>Rejected by You</p>
            </div>
        </div>

        <!-- Connection Requests Table -->
        <table>
            <thead>
                <tr>
                    <th>Parent Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Relationship</th>
                    <th>Your Decision</th>
                    <th>Admin Status</th>
                    <th>Requested On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($connectionRequests as $request)
                <tr>
                    <td>
                        <strong>{{ $request->outsideUser->fullname ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $request->outsideUser->email ?? 'N/A' }}</td>
                    <td>{{ $request->outsideUser->phone_number ?? 'N/A' }}</td>
                    <td>{{ $request->relationship }}</td>
                    <td>
                        <span class="status-badge status-{{ $request->inside_user_approval }}">
                            @if($request->inside_user_approval === 'accepted')
                                 Accepted
                            @elseif($request->inside_user_approval === 'rejected')
                                 Rejected
                            @else
                                 Pending
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $request->status }}">
                            @if($request->status === 'approved')
                                 Approved
                            @elseif($request->status === 'rejected')
                                 Rejected
                            @else
                                 Pending
                            @endif
                        </span>
                    </td>
                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($request->inside_user_approval === 'pending' && $request->status === 'pending')
                            <form action="{{ route('insideuser.connection.accept', $request->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-accept" onclick="return confirm('Accept this connection request?')">✓ Accept</button>
                            </form>
                            
                            <button class="btn btn-reject" onclick="openRejectModal({{ $request->id }})">✗ Reject</button>
                            
                            <!-- Reject Modal -->
                            <div id="rejectModal{{ $request->id }}" class="modal">
                                <div class="modal-content">
                                    <h3>Reject Connection Request</h3>
                                    <p>Are you sure you want to reject this connection request?</p>
                                    <form action="{{ route('insideuser.connection.reject', $request->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-group">
                                            <label for="remarks_{{ $request->id }}">Reason (Optional):</label>
                                            <textarea name="remarks" id="remarks_{{ $request->id }}" placeholder="Enter reason for rejection..."></textarea>
                                        </div>
                                        <div class="modal-actions">
                                            <button type="button" class="btn btn-back" onclick="closeRejectModal({{ $request->id }})">Cancel</button>
                                            <button type="submit" class="btn btn-reject">Reject Request</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif($request->inside_user_approval === 'accepted' && $request->status === 'pending')
                            <span style="color: #ff9800;">Waiting for admin approval</span>
                        @elseif($request->status === 'approved')
                            <span style="color: #4caf50;">✓ Connected</span>
                        @elseif($request->inside_user_approval === 'rejected')
                            <span style="color: #999;">Rejected</span>
                        @else
                            <span style="color: #999;">{{ ucfirst($request->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
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
