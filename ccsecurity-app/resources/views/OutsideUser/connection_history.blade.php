<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection History - School Security</title>
    @vite(['resources/css/OutsideUser/outside_user_connections_history.css'])
</head>
<body>
    <div class="history-container">
        <div class="glass-card">
            
            <div class="page-header">
                <div class="header-title">
                    <h1>Connection History</h1>
                    <p>View all your child connection requests</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('outsider.dashboard') }}" class="btn btn-outline">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.5rem;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Back
                    </a>
                    <a href="{{ route('outsideuser.connections.request') }}" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.5rem;"><path d="M5 12h14M12 5v14"/></svg>
                        New Connection
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ session('success') }}
            </div>
            @endif

            @if($connections->count() > 0)
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student / Inside User</th>
                            <th>Relationship</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Requested On</th>
                            <th>{{ ucfirst($connections->first()->status === 'approved' ? 'Approved' : 'Updated') }} On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($connections as $connection)
                        <tr>
                            <td>
                                <div class="user-identity">
                                    <span class="name">{{ $connection->insideUser->fullname ?? 'N/A' }}</span>
                                    <span class="email">{{ $connection->insideUser->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="relationship-tag">{{ $connection->relationship }}</span>
                            </td>
                            <td>
                                @if($connection->status === 'approved')
                                    <span class="status-badge status-approved">APPROVED</span>
                                @elseif($connection->status === 'rejected')
                                    <span class="status-badge status-rejected">REJECTED</span>
                                @else
                                    <span class="status-badge status-pending">PENDING</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $connection->admin_remarks ? $connection->admin_remarks : '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $connection->created_at->format('M d, Y') }}<br>
                                    <small>{{ $connection->created_at->format('h:i A') }}</small>
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    @if($connection->approved_at)
                                        {{ $connection->approved_at->format('M d, Y') }}<br>
                                        <small>{{ $connection->approved_at->format('h:i A') }}</small>
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($connection->status === 'approved')
                                    <form action="{{ route('outsideuser.connections.cancel.approved', $connection->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-cancel-approved" onclick="return confirm('Are you sure you want to cancel this connection?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            Cancel
                                        </button>
                                    </form>
                                @elseif($connection->status === 'pending')
                                    <form action="{{ route('outsideuser.connections.cancel', $connection->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-cancel-pending" onclick="return confirm('Are you sure you want to cancel this connection request?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            Cancel Request
                                        </button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($connections->hasPages())
            <div class="pagination-wrapper">
                {{ $connections->links() }}
            </div>
            @endif

            @else
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                <p>You haven't requested any connections yet.</p>
                <a href="{{ route('outsideuser.connections.request') }}" class="btn btn-primary">Request your first connection</a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
