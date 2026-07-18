<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inside User Details - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'inside_users'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>User <span class="highlight">Details</span></h1>
                <p class="subtitle">Inside user account information</p>
            </div>
            <a href="{{ $backUrl }}" class="btn-secondary">← Back</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; max-width:700px;">
            <h3>Account Information</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">First Name</div>
                    <div class="detail-value">{{ $inside_user->first_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Name</div>
                    <div class="detail-value">{{ $inside_user->last_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value">{{ $inside_user->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Role</div>
                    <div class="detail-value"><span class="badge role-badge">{{ ucfirst($inside_user->role) }}</span></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">QR Status</div>
                    <div class="detail-value">
                        @if($inside_user->qr_status === 'active')
                            <span class="badge status-active">Active</span>
                        @else
                            <span class="badge status-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created At</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($inside_user->created_at)->format('M d, Y') }}</div>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; gap:12px;">
                <a href="{{ route('admin.user.edit.form', ['id' => $inside_user->id, 'back_url' => $backUrl]) }}" class="btn-primary">Edit User</a>
                <a href="{{ $backUrl }}" class="btn-secondary">← Back to List</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
