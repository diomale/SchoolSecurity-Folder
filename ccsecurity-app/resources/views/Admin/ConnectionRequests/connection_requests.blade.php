<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent-Child Connections - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/AdminStyleFolder/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">CCSS</div>
            <div class="sidebar-brand">
                <strong>Columban College</strong>
                <span>Admin Portal</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Inside Users</span></a>
            <a href="{{ route('security.user.table.section') }}" class="nav-link"><span class="nav-icon">👮</span><span>Security Guards</span></a>
            <a href="{{ route('show.admin.outsider.list') }}" class="nav-link"><span class="nav-icon">👤</span><span>Outsider Management</span></a>
            <a href="{{ route('admin.visit.requests') }}" class="nav-link"><span class="nav-icon">📅</span><span>Visit Requests</span></a>
            <a href="{{ route('admin.connection.requests') }}" class="nav-link active"><span class="nav-icon">👨‍👩‍👧</span><span>Connections</span></a>
            <a href="{{ route('admin.events.pending') }}" class="nav-link"><span class="nav-icon">🎉</span><span>Events</span></a>
            <a href="{{ route('admin.qr.status.management') }}" class="nav-link"><span class="nav-icon">📱</span><span>QR Management</span></a>
            <a href="{{ route('admin.shift.management') }}" class="nav-link"><span class="nav-icon">🕐</span><span>Shift Management</span></a>
            <a href="{{ route('admin.cleanup.settings') }}" class="nav-link"><span class="nav-icon">🗑️</span><span>Cleanup Settings</span></a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn"><span class="nav-icon">🚪</span><span>Logout</span></button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Parent-Child <span class="highlight">Connections</span></h1>
                <p class="subtitle">View parent-to-student connection requests (student approval required)</p>
            </div>
        </div>

        <!-- Stats Row -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px;" class="fade-in">
            <div class="glass-card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px; border-left:4px solid var(--warning);">
                <div style="font-size:2rem;">⏳</div>
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Awaiting Student</div>
                    <div style="font-size:1.8rem; font-weight:800;">{{ $pendingCount }}</div>
                </div>
            </div>
            <div class="glass-card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px; border-left:4px solid var(--success);">
                <div style="font-size:2rem;">✅</div>
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Approved (Auto)</div>
                    <div style="font-size:1.8rem; font-weight:800;">{{ $approvedCount }}</div>
                </div>
            </div>
            <div class="glass-card" style="margin:0; padding:20px; display:flex; align-items:center; gap:16px; border-left:4px solid var(--danger);">
                <div style="font-size:2rem;">❌</div>
                <div>
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Rejected</div>
                    <div style="font-size:1.8rem; font-weight:800;">{{ $rejectedCount }}</div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fade-in">⚠ {{ session('error') }}</div>
        @endif

        <!-- Info notice -->
        <div class="alert alert-info fade-in" style="animation-delay:0.05s;">
            ℹ <strong>Note:</strong> Parent-child connections require <strong>student approval only</strong>. Admin approval is not required — connections with student acceptance are automatically approved.
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">👨‍👩‍👧 Connection Requests</h3>
            </div>

            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Parent / Visitor</th>
                            <th>Student / Child</th>
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
                                <div class="user-name">
                                    <div class="avatar-placeholder">{{ substr($connection->outsideUser->fullname ?? '?', 0, 1) }}</div>
                                    <div>
                                        <div style="font-weight:600;">{{ $connection->outsideUser->fullname ?? 'N/A' }}</div>
                                        <div style="font-size:0.82rem; color:var(--text-muted);">{{ $connection->outsideUser->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-name">
                                    <div class="avatar-placeholder" style="background: linear-gradient(135deg, var(--success), #34d399);">
                                        {{ substr($connection->insideUser->fullname ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;">{{ $connection->insideUser->fullname ?? 'N/A' }}</div>
                                        <div style="font-size:0.82rem; color:var(--text-muted);">{{ $connection->insideUser->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $connection->relationship }}</td>
                            <td>
                                @if($connection->inside_user_approval === 'accepted')
                                    <span class="badge status-approved">Accepted</span>
                                @elseif($connection->inside_user_approval === 'rejected')
                                    <span class="badge status-rejected">Rejected</span>
                                @else
                                    <span class="badge status-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($connection->status === 'approved')
                                    <span class="badge status-approved">Approved</span>
                                @elseif($connection->status === 'rejected')
                                    <span class="badge status-rejected">Rejected</span>
                                @else
                                    <span class="badge status-pending">Pending</span>
                                @endif
                            </td>
                            <td class="date-cell">{{ $connection->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">👨‍👩‍👧</div>
                                    <h3>No Connection Requests</h3>
                                    <p>No parent-child connection requests found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($connectionRequests->hasPages())
                <div style="padding: 16px 24px;">
                    <div class="pagination-container">{{ $connectionRequests->links() }}</div>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
