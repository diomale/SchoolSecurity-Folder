<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Super Admin Dashboard - CCSS</title>
    <!-- Modern Font: Outfit -->
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
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-inverse-muted);">Super Admin Portal</small></h2>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-label">System</div>
                <a href="{{ route('superadmin.dashboard') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">🛡️</span> Manage Admins
                </a>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('superadmin.logout') }}" style="width: 100%; margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon" style="font-size: 1.1rem;">🚪</span> Secure Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Super Admin <span class="highlight">Command</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Welcome, {{ auth('superadmin')->user()->name }}. Manage system administrators below.</p>
                </div>
                <div class="header-actions fade-in" style="animation-delay: 0.2s;">
                    <a href="{{ route('superadmin.admin.show.add.form') }}" class="btn-primary">
                        <span>➕</span> Create New Admin
                    </a>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Overview Statistics -->
            <div class="stats-grid fade-in" style="animation-delay: 0.2s;">
                <div class="stat-card" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-left: 4px solid var(--primary);">
                    <div class="stat-icon" style="color: var(--primary);">🛡️</div>
                    <div class="stat-info">
                        <h3>Total Admins</h3>
                        <p class="stat-value">{{ $totalAdmins ?? 0 }}</p>
                    </div>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%); border-left: 4px solid var(--success);">
                    <div class="stat-icon" style="color: var(--success);">👮</div>
                    <div class="stat-info">
                        <h3>Security Guards</h3>
                        <p class="stat-value">{{ $totalGuards ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(56, 189, 248, 0.1) 100%); border-left: 4px solid var(--secondary);">
                    <div class="stat-icon" style="color: var(--secondary);">🎓</div>
                    <div class="stat-info">
                        <h3>Inside Users</h3>
                        <p class="stat-value">{{ $totalInsideUsers ?? 0 }}</p>
                    </div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%); border-left: 4px solid var(--warning);">
                    <div class="stat-icon" style="color: var(--warning);">👤</div>
                    <div class="stat-info">
                        <h3>Outside Users</h3>
                        <p class="stat-value">{{ $totalOutsideUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin List Card -->
            <div class="glass-card fade-in" style="animation-delay: 0.3s; padding: 0; overflow: hidden; margin-top: 30px;">
                <h2 style="padding: 24px 24px 10px; margin: 0; font-size: 1.25rem; color: var(--text-main); border-bottom: 1px solid rgba(0,0,0,0.05);">System Administrators</h2>
                
                @if($admins->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Administrator</th>
                                <th>Contact Email</th>
                                <th style="text-align: right;">Management Actions</th>
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
                                            <span style="font-size: 0.85rem; color: var(--text-muted);">Admin ID: #{{ $admin->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="color: var(--text-main); font-size: 0.95rem;">{{ $admin->email }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: flex-end;">
                                        <form action="{{ route('superadmin.admin.show', $admin->id) }}" style="margin:0;">
                                            <button type="submit" class="action-btn btn-view" title="View Details">👁️ View</button>
                                        </form>
                                        
                                        <form action="{{ route('superadmin.admin.edit', $admin->id) }}" style="margin:0;">
                                            <button type="submit" class="action-btn btn-edit" title="Edit Admin">✏️ Edit</button>
                                        </form>
                                        
                                        <form action="{{ route('superadmin.admin.delete', $admin->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn btn-delete" onclick="return confirm('WARNING: Are you sure you want to delete administrator {{ $admin->name }}? This action cannot be undone.')" title="Delete Admin">🗑️ Delete</button>
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
                    <div class="empty-icon">🛡️</div>
                    <p style="font-size: 1.1rem; color: var(--text-main); font-weight: 600;">No Administrators Found</p>
                    <span style="display: block; font-size: 0.95rem; margin-top: 5px;">Click the 'Create New Admin' button to add the first system administrator.</span>
                </div>
                @endif
            </div>

        </main>
    </div>
</body>
</html>
