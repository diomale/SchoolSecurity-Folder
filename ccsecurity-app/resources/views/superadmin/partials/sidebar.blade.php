<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <strong>KitaKits</strong>
            <span>Super Admin Portal</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">System</div>
        <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ Request::routeIs('superadmin.dashboard', 'superadmin.admin.show', 'superadmin.admin.edit', 'superadmin.admin.update', 'superadmin.admin.delete') ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('superadmin.admin.show.add.form') }}" class="nav-link {{ Request::routeIs('superadmin.admin.show.add.form', 'superadmin.storeAdmin') ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            <span>Create Admin</span>
        </a>
        <a href="{{ route('superadmin.logs') }}" class="nav-link {{ Request::routeIs('superadmin.logs') ? 'active' : '' }}">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            <span>Logs</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('superadmin.logout') }}" style="width: 100%; margin: 0;">
            @csrf
            <button type="submit" class="logout-btn">
                <svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Secure Logout</span>
            </button>
        </form>
    </div>
</aside>
