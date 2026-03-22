<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/AdminStyleFolder/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">CCSS</div>
            <div class="sidebar-brand"><strong>Columban College</strong><span>Admin Portal</span></div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Inside Users</span></a>
            <a href="{{ route('security.user.table.section') }}" class="nav-link"><span class="nav-icon">👮</span><span>Security Guards</span></a>
            <a href="{{ route('show.admin.outsider.list') }}" class="nav-link"><span class="nav-icon">👤</span><span>Outsider Management</span></a>
            <a href="{{ route('admin.visit.requests') }}" class="nav-link"><span class="nav-icon">📅</span><span>Visit Requests</span></a>
            <a href="{{ route('admin.connection.requests') }}" class="nav-link"><span class="nav-icon">👨‍👩‍👧</span><span>Connections</span></a>
            <a href="{{ route('admin.events.pending') }}" class="nav-link"><span class="nav-icon">🎉</span><span>Events</span></a>
            <a href="{{ route('admin.qr.status.management') }}" class="nav-link"><span class="nav-icon">📱</span><span>QR Management</span></a>
            <a href="{{ route('admin.shift.management') }}" class="nav-link"><span class="nav-icon">🕐</span><span>Shift Management</span></a>
            <a href="{{ route('admin.cleanup.settings') }}" class="nav-link"><span class="nav-icon">🗑️</span><span>Cleanup Settings</span></a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">@csrf
                <button type="submit" class="logout-btn"><span class="nav-icon">🚪</span><span>Logout</span></button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>System <span class="highlight">Administration</span></h1>
                <p class="subtitle">Welcome back, <strong><a href="{{ route('admin.profile.show') }}" style="color:var(--primary); text-decoration:none; font-weight:700;">{{ auth('admin')->user()->name }}</a></strong></p>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <div style="text-align:right;">
                    <div style="font-size:0.85rem; font-weight:700; color:var(--text-main);">{{ now()->format('l, M j') }}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ now()->format('h:i A') }}</div>
                </div>
                <div class="avatar-placeholder" style="margin:0; cursor:pointer;" onclick="location.href='{{ route('admin.profile.show') }}'">
                    {{ substr(auth('admin')->user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s;">
            <h3>🛡️ Management Overview</h3>
            <p style="color:var(--text-muted); margin-bottom:24px; font-size:0.95rem;">Select a section below to manage system records and approvals.</p>
            
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                <!-- Inside User Management -->
                <a href="{{ route('admin.show.crudSection') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--bg-main); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--primary-light); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">🎓</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Inside Users</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Manage students & staff records.</p>
                    </div>
                </a>

                <!-- Security Guard Management -->
                <a href="{{ route('security.user.table.section') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--info-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--info); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">👮</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Security Personnel</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Accounts and access control.</p>
                    </div>
                </a>

                <!-- Outsider Management -->
                <a href="{{ route('show.admin.outsider.list') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--warning-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--warning); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">👤</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Visitor Database</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Oversee external accounts.</p>
                    </div>
                </a>

                <!-- Visit Requests -->
                <a href="{{ route('admin.visit.requests') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--success-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--success); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">📅</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Visit Requests</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Approve campus visit passes.</p>
                    </div>
                </a>

                <!-- Parent-Child Connections -->
                <a href="{{ route('admin.connection.requests') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--purple-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--purple); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">👨‍👩‍👧</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Connections</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Verify family relations.</p>
                    </div>
                </a>

                <!-- Event Management -->
                <a href="{{ route('admin.events.pending') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--orange-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--orange); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">🎉</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Event Control</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Campus event approvals.</p>
                    </div>
                </a>

                <!-- QR Status -->
                <a href="{{ route('admin.qr.status.management') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--info-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--info); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">📱</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">QR Management</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Activate/Deactivate ID QR.</p>
                    </div>
                </a>

                <!-- Shift Management -->
                <a href="{{ route('admin.shift.management') }}" style="text-decoration:none; display:flex; gap:16px; padding:20px; border-radius:var(--radius-md); border:1px solid rgba(0,0,0,0.05); background:var(--primary-light); transition:var(--transition);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-hover)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                    <div style="width:48px; height:48px; border-radius:12px; background:var(--primary); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">🕐</div>
                    <div>
                        <h4 style="margin:0 0 4px 0; color:var(--text-main); font-size:1rem;">Shift Scheds</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.4;">Guard duty assignments.</p>
                    </div>
                </a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;" class="fade-in" style="animation-delay:0.2s;">
             <div class="glass-card" style="margin:0;">
                <h3>📊 System Status</h3>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);">Database Status</span>
                        <span class="badge status-active">Online</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);">Auto-Cleanup</span>
                        <span class="badge status-active">Daily @ 12AM</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);">Active Sessions</span>
                        <span class="badge role-badge">Internal Only</span>
                    </div>
                </div>
            </div>
            <div class="glass-card" style="margin:0;">
                <h3>🛡️ Security Note</h3>
                <p style="margin:0; font-size:0.9rem; color:var(--text-muted); line-height:1.6;">
                    This portal is restricted to <strong>System Administrators</strong> only. Ensure you log out after each session to maintain school security integrity. All administrative actions are logged for audit purposes.
                </p>
                <div style="margin-top:16px;">
                    <a href="{{ route('admin.cleanup.settings') }}" class="btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger-light);">View Retention Policies</a>
                </div>
            </div>
        </div>

        <footer style="margin-top:auto; padding-top:40px; text-align:center; color:var(--text-light); font-size:0.85rem;">
            &copy; {{ date('Y') }} Columban College Security System • Premium Admin Portal
        </footer>
    </main>
</div>
</body>
</html>
