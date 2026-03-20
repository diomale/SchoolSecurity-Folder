<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/AdminStyleFolder/admin_style_dashboard.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>

            <nav class="sidebar-nav">
                <button class="tab-button active">
                    <span class="nav-icon">🛡️</span> Admin Panel
                </button>
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('admin.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">System <span class="highlight">Administration</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Welcome, <strong><a href="{{ route('admin.profile.show') }}" class="profile-link">{{ auth('admin')->user()->name }}</a></strong></p>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="tab-content active">
                
                <div class="glass-card full-width fade-in" style="animation-delay: 0.2s;">
                    <h3>Management Sections</h3>
                    <div class="actions-grid">
                        
                        <!-- Inside User Management -->
                        <a href="{{ route('admin.show.crudSection') }}" class="action-card">
                            <div class="action-icon bg-primary">🎓</div>
                            <div class="action-text">
                                <h4>Inside User Management</h4>
                                <p>Manage students, teachers, and internal staff records.</p>
                            </div>
                        </a>

                        <!-- Security Guard Management -->
                        <a href="{{ route('security.user.table.section') }}" class="action-card">
                            <div class="action-icon bg-info">👮</div>
                            <div class="action-text">
                                <h4>Security Guard Management</h4>
                                <p>Add, edit, or remove security personnel accounts.</p>
                            </div>
                        </a>

                        <!-- Outsider Management -->
                        <a href="{{ route('show.admin.outsider.list') }}" class="action-card">
                            <div class="action-icon bg-warning">👤</div>
                            <div class="action-text">
                                <h4>Outsider Management</h4>
                                <p>Oversee visitor accounts and external passes.</p>
                            </div>
                        </a>

                        <!-- Visit Requests -->
                        <a href="{{ route('admin.visit.requests') }}" class="action-card">
                            <div class="action-icon bg-success">📅</div>
                            <div class="action-text">
                                <h4>Visit Requests</h4>
                                <p>Review and approve pending campus visit requests.</p>
                            </div>
                        </a>

                        <!-- Parent-Child Connections -->
                        <a href="{{ route('admin.connection.requests') }}" class="action-card">
                            <div class="action-icon bg-purple">👨‍👩‍👧</div>
                            <div class="action-text">
                                <h4>Parent-Child Connections</h4>
                                <p>Verify requests linking parents to student accounts.</p>
                            </div>
                        </a>

                        <!-- Event Management -->
                        <a href="{{ route('admin.events.pending') }}" class="action-card">
                            <div class="action-icon bg-orange">🎉</div>
                            <div class="action-text">
                                <h4>Event Management</h4>
                                <p>Approve and manage public and internal events.</p>
                            </div>
                        </a>

                        <!-- Cleanup Settings -->
                        <a href="{{ route('admin.cleanup.settings') }}" class="action-card">
                            <div class="action-icon bg-danger">🗑️</div>
                            <div class="action-text">
                                <h4>Auto-Delete Settings</h4>
                                <p>Configure automated data cleanup and retention policies.</p>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="admin-footer fade-in" style="animation-delay: 0.3s;">
                <p>&copy; {{ date('Y') }} School Security System</p>
            </footer>

        </main>
    </div>
</body>
</html>
