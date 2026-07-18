<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-circle">CCSS</div>
        <div class="sidebar-brand"><strong>Columban College</strong><span>Admin Portal</span></div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $activePage === 'dashboard' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.show.crudSection') }}" class="nav-link {{ $activePage === 'inside_users' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span>Inside Users</span>
        </a>
        <a href="{{ route('security.user.table.section') }}" class="nav-link {{ $activePage === 'security_guards' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M12 6v6l4 2"/></svg>
            <span>Security Guards</span>
        </a>
        <a href="{{ route('show.admin.outsider.list') }}" class="nav-link {{ $activePage === 'outsider_management' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Outsider Management</span>
        </a>
        <a href="{{ route('admin.visit.requests') }}" class="nav-link {{ $activePage === 'visit_requests' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>Visit Requests</span>
        </a>
        <a href="{{ route('admin.connection.requests') }}" class="nav-link {{ $activePage === 'connections' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Connections</span>
        </a>
        <a href="{{ route('admin.events.pending') }}" class="nav-link {{ $activePage === 'events' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <span>Events</span>
        </a>
        <a href="{{ route('admin.event-privileges.index') }}" class="nav-link {{ $activePage === 'event_privileges' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Event Privileges</span>
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ $activePage === 'activity_logs' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Activity Logs</span>
        </a>
        <a href="{{ route('admin.qr.status.management') }}" class="nav-link {{ $activePage === 'qr_management' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="6" height="6" rx="1"/><rect x="16" y="2" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="16" y="16" width="6" height="6" rx="1"/></svg>
            <span>QR Management</span>
        </a>
        <a href="{{ route('admin.shift.management') }}" class="nav-link {{ $activePage === 'shift_management' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Shift Management</span>
        </a>
        <a href="{{ route('admin.cleanup.settings') }}" class="nav-link {{ $activePage === 'cleanup_settings' ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            <span>Cleanup Settings</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-profile-mini">
            <div class="profile-avatar">{{ substr(auth('admin')->user()->name, 0, 1) }}</div>
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
