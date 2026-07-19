<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">CCSS</div>
            <div class="sidebar-brand"><strong>Columban College</strong><span>Admin Portal</span></div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                <span>Inside Users</span>
            </a>
            <a href="{{ route('security.user.table.section') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M12 6v6l4 2"/></svg>
                <span>Security Guards</span>
            </a>
            <a href="{{ route('show.admin.outsider.list') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Outsider Management</span>
            </a>
            <a href="{{ route('admin.visit.requests') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Visit Requests</span>
            </a>
            <a href="{{ route('admin.connection.requests') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Connections</span>
            </a>
            <a href="{{ route('admin.events.pending') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Events</span>
            </a>
            <a href="{{ route('admin.qr.status.management') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="6" height="6" rx="1"/><rect x="16" y="2" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="16" y="16" width="6" height="6" rx="1"/></svg>
                <span>QR Management</span>
            </a>
            <a href="{{ route('admin.shift.management') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Shift Management</span>
            </a>
            <a href="{{ route('admin.cleanup.settings') }}" class="nav-link">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                <span>Cleanup Settings</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-profile-mini">
                <span style="color: #000; font-weight: 600;">{{ auth('admin')->user()->name }}</span>
                <div class="profile-info">
                    <span class="profile-name">{{ auth('admin')->user()->name }}</span>
                    <span class="profile-role">Admin</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf
                <button type="submit" class="logout-btn">
                    <svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>System <span class="highlight">Administration</span></h1>
                <p class="subtitle">Welcome back, <strong><a href="{{ route('admin.profile.show') }}" style="color:var(--primary); text-decoration:none; font-weight:700;">{{ auth('admin')->user()->name }}</a></strong></p>
            </div>
            <div class="header-right">
                <div class="datetime-display">
                    <div class="date">{{ now()->format('l, M j, Y') }}</div>
                    <div class="time">{{ now()->format('h:i A') }}</div>
                </div>
                <span style="color: #000; font-weight: 600;" onclick="location.href='{{ route('admin.profile.show') }}'">
                    {{ auth('admin')->user()->name }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in" style="animation-delay: 0.1s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Overview Statistics -->
        <div class="stats-grid fade-in" style="animation-delay: 0.15s;">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Students</h3>
                    <p class="stat-value">{{ $totalInsideUsers ?? 0 }}</p>
                </div>
            </div>

            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Guards</h3>
                    <p class="stat-value">{{ $totalGuards ?? 0 }}</p>
                    <span class="stat-badge">{{ $activeGuards ?? 0 }} active</span>
                </div>
            </div>

            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Visitors</h3>
                    <p class="stat-value">{{ $totalOutsideUsers ?? 0 }}</p>
                </div>
            </div>

            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Inside Campus</h3>
                    <p class="stat-value">{{ $currentlyInside ?? 0 }}</p>
                </div>
            </div>

            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Visit Requests</h3>
                    <p class="stat-value">{{ $pendingVisitRequests ?? 0 }}</p>
                    <span class="stat-badge stat-badge-warning">pending</span>
                </div>
            </div>

            <div class="stat-card stat-orange">
                <div class="stat-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-info">
                    <h3>Connections</h3>
                    <p class="stat-value">{{ $pendingConnections ?? 0 }}</p>
                    <span class="stat-badge stat-badge-warning">pending</span>
                </div>
            </div>
        </div>

        <!-- Management Overview -->
        <div class="glass-card fade-in" style="animation-delay:0.25s;">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Management Overview
            </h3>
            <p style="color:var(--text-muted); margin-bottom:24px; font-size:0.95rem;">Select a section below to manage system records and approvals.</p>

            <div class="actions-grid">
                <a href="{{ route('admin.show.crudSection') }}" class="action-card">
                    <div class="action-icon bg-primary">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Inside Users</h4>
                        <p>Manage students & staff records</p>
                    </div>
                </a>

                <a href="{{ route('security.user.table.section') }}" class="action-card">
                    <div class="action-icon bg-info">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Security Personnel</h4>
                        <p>Accounts and access control</p>
                    </div>
                </a>

                <a href="{{ route('show.admin.outsider.list') }}" class="action-card">
                    <div class="action-icon bg-warning">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Visitor Database</h4>
                        <p>Oversee external accounts</p>
                    </div>
                </a>

                <a href="{{ route('admin.visit.requests') }}" class="action-card">
                    <div class="action-icon bg-success">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Visit Requests</h4>
                        <p>Approve campus visit passes</p>
                    </div>
                </a>

                <a href="{{ route('admin.connection.requests') }}" class="action-card">
                    <div class="action-icon bg-purple">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Connections</h4>
                        <p>Verify family relations</p>
                    </div>
                </a>

                <a href="{{ route('admin.events.pending') }}" class="action-card">
                    <div class="action-icon bg-orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Event Control</h4>
                        <p>Campus event approvals</p>
                    </div>
                </a>

                <a href="{{ route('admin.qr.status.management') }}" class="action-card">
                    <div class="action-icon bg-info">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="6" height="6" rx="1"/><rect x="16" y="2" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="16" y="16" width="6" height="6" rx="1"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>QR Management</h4>
                        <p>Activate/Deactivate ID QR</p>
                    </div>
                </a>

                <a href="{{ route('admin.shift.management') }}" class="action-card">
                    <div class="action-icon bg-primary">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="action-text">
                        <h4>Shift Scheds</h4>
                        <p>Guard duty assignments</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- System Status & Security Note -->
        <div class="content-row fade-in" style="animation-delay: 0.35s;">
            <div class="glass-card" style="margin:0;">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    System Status
                </h3>
                <div class="status-list">
                    <div class="status-item">
                        <span class="status-label">Database</span>
                        <span class="badge status-active">Online</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Auto-Cleanup</span>
                        <span class="badge status-active">Daily @ 12AM</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Active Sessions</span>
                        <span class="badge role-badge">Internal Only</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Entry Logs</span>
                        <span class="badge role-badge">{{ $totalEntryLogs ?? 0 }} total</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Events</span>
                        <span class="badge role-badge">{{ $totalEvents ?? 0 }} total</span>
                    </div>
                </div>
            </div>

            <div class="glass-card" style="margin:0;">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Security Note
                </h3>
                <p style="margin:0; font-size:0.9rem; color:var(--text-muted); line-height:1.6;">
                    This portal is restricted to <strong>System Administrators</strong> only. Ensure you log out after each session to maintain school security integrity. All administrative actions are logged for audit purposes.
                </p>
                <div style="margin-top:16px;">
                    <a href="{{ route('admin.cleanup.settings') }}" class="btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger-light);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        View Retention Policies
                    </a>
                </div>
            </div>
        </div>

        <footer class="dashboard-footer fade-in" style="animation-delay: 0.4s;">
            &copy; {{ date('Y') }} Columban College Security System &bull; Admin Portal
        </footer>
    </main>
</div>
</body>
</html>
