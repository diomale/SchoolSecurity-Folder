<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Details - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'security_guards'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Guard <span class="highlight">Details</span></h1>
                <p class="subtitle">Security guard account information</p>
            </div>
            <a href="{{ $backUrl }}" class="btn-secondary">← Back</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; max-width:680px;">
            <h3>Guard Information</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">First Name</div>
                    <div class="detail-value">{{ $security_guard_user->first_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Name</div>
                    <div class="detail-value">{{ $security_guard_user->last_name }}</div>
                </div>
                <div class="detail-item" style="grid-column: span 2;">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value">{{ $security_guard_user->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account Status</div>
                    <div class="detail-value">
                        @if(($security_guard_user->status ?? 1) == 1)
                            <span class="badge status-active">Active</span>
                        @else
                            <span class="badge status-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created At</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($security_guard_user->created_at)->format('M d, Y') }}</div>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; gap:12px;">
                <a href="{{ route('security.guard.user.edit', ['id' => $security_guard_user->id, 'back_url' => $backUrl]) }}" class="btn-primary">Edit Guard</a>
                <a href="{{ $backUrl }}" class="btn-secondary">← Back to List</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
