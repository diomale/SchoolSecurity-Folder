<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin Dashboard - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css','resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">SA</div>
                <div class="sidebar-brand">
                    <strong>Columban College</strong>
                    <span>Super Admin Portal</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-label">System</div>
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link active">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('superadmin.admin.show.add.form') }}" class="nav-link">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    <span>Create Admin</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile-mini">
                    <div class="profile-avatar">{{ substr(auth('superadmin')->user()->name, 0, 1) }}</div>
                    <div class="profile-info">
                        <span class="profile-name">{{ auth('superadmin')->user()->name }}</span>
                        <span class="profile-role">Super Admin</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('superadmin.logout') }}" style="width: 100%; margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span>Secure Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header fade-in">
                <div class="header-left">
                    <h1>Super Admin <span class="highlight">Command</span></h1>
                    <p class="subtitle">Welcome back, <strong>{{ auth('superadmin')->user()->name }}</strong>. System overview below.</p>
                </div>
                <div class="header-right">
                    <div class="datetime-display">
                        <div class="date">{{ now()->format('l, M j, Y') }}</div>
                        <div class="time">{{ now()->format('h:i A') }}</div>
                    </div>
                    <div class="header-avatar" onclick="location.href='{{ route('superadmin.dashboard') }}'">
                        {{ substr(auth('superadmin')->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success fade-in" style="animation-delay: 0.1s;">
                    <div class="alert-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Overview Statistics -->
            <div class="stats-grid fade-in" style="animation-delay: 0.15s;">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Admins</h3>
                        <p class="stat-value">{{ $totalAdmins ?? 0 }}</p>
                    </div>
                </div>
                
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"></path><path d="M12 6v6l4 2"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Guards</h3>
                        <p class="stat-value">{{ $totalGuards ?? 0 }}</p>
                        <span class="stat-badge">{{ $activeGuards ?? 0 }} active</span>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Students</h3>
                        <p class="stat-value">{{ $totalInsideUsers ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Visitors</h3>
                        <p class="stat-value">{{ $totalOutsideUsers ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-purple">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Inside Campus</h3>
                        <p class="stat-value">{{ $currentlyInside ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-orange">
                    <div class="stat-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div class="stat-info">
                        <h3>Visit Requests</h3>
                        <p class="stat-value">{{ $pendingVisitRequests ?? 0 }}</p>
                        <span class="stat-badge stat-badge-warning">pending</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & System Status Row -->
            <div class="content-row fade-in" style="animation-delay: 0.25s;">
                <!-- Quick Actions -->
                <div class="glass-card quick-actions-card">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="{{ route('superadmin.admin.show.add.form') }}" class="action-card">
                            <div class="action-icon bg-primary">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                            </div>
                            <div class="action-text">
                                <h4>Create Admin</h4>
                                <p>Add new system administrator</p>
                            </div>
                        </a>

                        <a href="{{ route('superadmin.admin.show.add.form') }}" class="action-card">
                            <div class="action-icon bg-success">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                            <div class="action-text">
                                <h4>Manage Admins</h4>
                                <p>View, edit, or remove admins</p>
                            </div>
                        </a>

                        <a href="{{ route('superadmin.dashboard') }}" class="action-card">
                            <div class="action-icon bg-info">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </div>
                            <div class="action-text">
                                <h4>System Overview</h4>
                                <p>Monitor system health</p>
                            </div>
                        </a>

                        <a href="{{ route('superadmin.dashboard') }}" class="action-card">
                            <div class="action-icon bg-warning">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <div class="action-text">
                                <h4>Security Audit</h4>
                                <p>Review access logs</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="glass-card system-status-card">
                    <h3>System Status</h3>
                    <div class="status-list">
                        <div class="status-item">
                            <span class="status-label">Database</span>
                            <span class="badge badge-success">Online</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Primary DB</span>
                            <span class="badge badge-success">ccsecurity_db</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Secondary DB</span>
                            <span class="badge badge-success">securitysystem</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Session Driver</span>
                            <span class="badge badge-info">Database</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Mail Driver</span>
                            <span class="badge badge-info">SMTP</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Active Sessions</span>
                            <span class="badge badge-primary">{{ $totalAdmins ?? 0 }} admins</span>
                        </div>
                    </div>

                    <div class="security-note">
                        <h4>Security Note</h4>
                        <p>This portal is restricted to <strong>Super Administrators</strong> only. All actions are logged for audit. Always log out after each session.</p>
                    </div>
                </div>
            </div>

            <!-- Admin Management Table -->
            <div class="glass-card fade-in" style="animation-delay: 0.35s; padding: 0; overflow: hidden;">
                <div class="card-header">
                    <h3>System Administrators</h3>
                    <a href="{{ route('superadmin.admin.show.add.form') }}" class="btn-primary btn-sm">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add New
                    </a>
                </div>
                
                @if($admins->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Administrator</th>
                                <th>Contact Email</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ substr($admin->name, 0, 1) }}</div>
                                        <div class="user-info">
                                            <span class="user-name">{{ $admin->name }}</span>
                                            <span class="user-id">Admin #{{ $admin->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="email-text">{{ $admin->email }}</span>
                                </td>
                                <td>
                                    @if($admin->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: flex-end;">
                                        <a href="{{ route('superadmin.admin.show', $admin->id) }}" class="action-btn btn-view" title="View Details">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            View
                                        </a>
                                        <a href="{{ route('superadmin.admin.edit', $admin->id) }}" class="action-btn btn-edit" title="Edit Admin">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('superadmin.admin.delete', $admin->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn btn-delete" onclick="return confirm('WARNING: Are you sure you want to delete administrator {{ $admin->name }}? This action cannot be undone.')" title="Delete Admin">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <p class="empty-title">No Administrators Found</p>
                    <p class="empty-desc">Click the 'Add New' button to create the first system administrator.</p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <footer class="dashboard-footer fade-in" style="animation-delay: 0.4s;">
                &copy; {{ date('Y') }} Columban College Security System &bull; Super Admin Portal
            </footer>
        </main>
    </div>
</body>
</html>
