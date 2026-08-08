<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Superadmin/superadmin_style_dashboard.css', 'resources/css/Superadmin/superadmin_style_add_form.css', 'resources/css/Superadmin/superadmin_style_edit.css', 'resources/js/app.js'])
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
                    <h1 class="fade-in">Edit <span class="highlight">Administrator</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Modify information for {{ $admin->name }}</p>
                </div>
            </header>

            <div class="form-glass-container fade-in" style="animation-delay: 0.2s;">
                @if ($errors->any())
                    <div class="error-list">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('superadmin.admin.update', ['id' => $admin->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="{{ $admin->name }}" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ $admin->email }}" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="form-group">
                        <label for="status">Account Status</label>
                        <select name="status" id="status">
                            <option value="1" {{ old('status', $admin->status ?? 1) == 1 ? 'selected' : '' }}>🟢 Active</option>
                            <option value="0" {{ old('status', $admin->status ?? 1) == 0 ? 'selected' : '' }}>🔴 Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary" style="margin: 0; background: var(--success);">
                            💾 Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
