<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Pass Management - Security Guard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
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
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-form button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        table tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        .badge-used {
            background: #e2e3e5;
            color: #383d41;
        }
        .badge-expired {
            background: #f8d7da;
            color: #721c24;
        }
        .purpose-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
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
        <!-- Header -->
        <div class="header">
            <div>
                <h1>🎫 Quick Pass Management</h1>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                    Today's Temporary Visitor Passes - {{ today()->format('M d, Y') }}
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('security.dashboard') }}" class="btn btn-sm" style="background: #6c757d; color: white;">← Back to Dashboard</a>
                <a href="{{ route('security.quick-pass.create') }}" class="btn btn-primary">+ New Quick Pass</a>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="message success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="message error">
            ⚠ {{ session('error') }}
        </div>
        @endif

        <!-- Search Form -->
        <form action="{{ route('security.quick-pass.list') }}" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by visitor name, vehicle, purpose, or QR..." value="{{ request('search') }}">
            <button type="submit">Search</button>
            @if(request('search'))
            <a href="{{ route('security.quick-pass.list') }}" class="btn btn-sm" style="background: #6c757d; color: white; align-self: center;">Clear</a>
            @endif
        </form>

        <!-- Quick Passes Table -->
        @if($quickPasses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>QR Code</th>
                    <th>Visitor Name</th>
                    <th>Vehicle Plate</th>
                    <th>Purpose</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Expires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quickPasses as $pass)
                <tr>
                    <td>
                        <code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                            {{ $pass->qr_value }}
                        </code>
                    </td>
                    <td><strong>{{ $pass->visitor_name }}</strong></td>
                    <td>{{ $pass->vehicle_plate ?? '—' }}</td>
                    <td>
                        <span class="purpose-badge" style="background: {{ $pass->purpose_color }};">
                            {{ $pass->purpose }}
                        </span>
                    </td>
                    <td>{{ $pass->created_at?->format('h:i A') ?? 'N/A' }}</td>
                    <td>
                        @if($pass->status === 'active')
                            @if($pass->isExpired())
                                <span class="badge badge-expired">Expired</span>
                            @else
                                <span class="badge badge-active">Active</span>
                            @endif
                        @elseif($pass->status === 'used')
                            <span class="badge badge-used">Used</span>
                        @else
                            <span class="badge badge-expired">Expired</span>
                        @endif
                    </td>
                    <td>
                        <small style="color: {{ $pass->isExpired() ? '#dc3545' : '#28a745' }};">
                            {{ $pass->expires_at->format('h:i A') }}
                        </small>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('security.quick-pass.qr', $pass->id) }}" class="btn btn-sm btn-primary">View QR</a>
                            <form action="{{ route('security.quick-pass.delete', $pass->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this Quick Pass?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if($quickPasses->hasPages())
        <div class="pagination">
            {{ $quickPasses->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="empty-state">
            <h2 style="margin-bottom: 10px;">🎫 No Quick Passes Today</h2>
            <p>No temporary visitor passes have been created today.</p>
            <a href="{{ route('security.quick-pass.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                + Create Your First Quick Pass
            </a>
        </div>
        @endif
    </div>
</body>
</html>
