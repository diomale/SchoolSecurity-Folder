<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Details - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css', 'resources/css/Superadmin/superadmin_style_details.css', 'resources/js/app.js'])
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
            <a href="{{ route('superadmin.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                &larr; Back to Dashboard
            </a>

            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Administrator <span class="highlight">Details</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Viewing information for Admin ID #{{ $admin->id }}</p>
                </div>
            </header>

            <div class="details-glass-container fade-in" style="animation-delay: 0.2s;">
                <div class="details-header">
                    <div class="big-avatar">{{ substr($admin->name, 0, 1) }}</div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.8rem; color: var(--primary-dark);">{{ $admin->name }}</h2>
                        <span style="color: var(--success); font-weight: 600; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px;">
                            <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span> Active System Admin
                        </span>
                    </div>
                </div>

                <ul class="details-list">
                    <li>
                        <strong>Full Name</strong>
                        <span>{{ $admin->name }}</span>
                    </li>
                    <li>
                        <strong>Email Address</strong>
                        <span>{{ $admin->email }}</span>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
