<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'dashboard'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Admin <span class="highlight">Profile</span></h1>
                <p class="subtitle">Your account details and personal settings</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Back to Dashboard</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; max-width:650px;">
            <div style="display:flex; align-items:center; gap:24px; margin-bottom:30px; padding-bottom:24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <div>
                    <h2 style="margin:0; border:0; padding:0; font-size:1.8rem; font-weight:800;">{{ auth('admin')->user()->name }}</h2>
                    <span class="badge role-badge" style="font-size:0.85rem; padding:6px 14px;">System Administrator</span>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item" style="grid-column: span 2;">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value" style="font-size:1.1rem;">{{ auth('admin')->user()->name }}</div>
                </div>
                <div class="detail-item" style="grid-column: span 2;">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value" style="font-size:1.1rem;">{{ auth('admin')->user()->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account Status</div>
                    <div class="detail-value"><span class="badge status-active">Active</span></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Login</div>
                    <div class="detail-value" style="font-size:0.95rem; color:var(--text-muted);">{{ now()->format('M d, Y h:i A') }}</div>
                </div>
            </div>

            <div style="margin-top:30px; padding:20px; background:var(--bg-main); border-radius:var(--radius-sm); border-left:4px solid var(--info);">
                <h4 style="margin:0 0 8px 0; color:var(--info);">Account Security</h4>
                <p style="margin:0; font-size:0.92rem; color:var(--text-muted); line-height:1.5;">To change your password or update your email address, please contact the system administrator or check the security settings guide.</p>
            </div>

            <div style="display:flex; gap:12px; margin-top:30px;">
                <form method="POST" action="{{ route('admin.logout') }}" style="width:100%;">
                    @csrf
                    <button type="submit" class="btn-danger btn-block" style="padding:14px;">
                        Sign Out of Account
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
