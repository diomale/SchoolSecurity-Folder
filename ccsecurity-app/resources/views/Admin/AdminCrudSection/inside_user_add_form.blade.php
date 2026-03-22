<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inside User - CCSS Admin</title>
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
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link active"><span class="nav-icon">🎓</span><span>Inside Users</span></a>
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
                <h1>Add <span class="highlight">Inside User</span></h1>
                <p class="subtitle">Register a new student, staff, or security guard account</p>
            </div>
            <a href="{{ route('admin.show.crudSection') }}" class="btn-secondary">← Back to List</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; max-width:680px;">
            <h3>🎓 New User Registration</h3>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px;">
                    @foreach ($errors->all() as $error)<div>⚠ {{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.add.user.accept') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}" required placeholder="e.g. John">
                        @error('first_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Last Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required placeholder="e.g. Doe">
                        @error('last_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required autocomplete="off" placeholder="john.doe@example.com">
                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" class="form-input" required autocomplete="off" placeholder="Enter secure password">
                    @error('password')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>User Role <span style="color:var(--danger)">*</span></label>
                        <select name="role" class="form-select">
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="staff"   {{ old('role') === 'staff'   ? 'selected' : '' }}>Staff</option>
                            <option value="security_guard" {{ old('role') === 'security_guard' ? 'selected' : '' }}>Security Guard</option>
                        </select>
                        @error('role')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>QR Status</label>
                        <select name="qr_status" class="form-select">
                            <option value="active"   {{ old('qr_status') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('qr_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('qr_status')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="submit" class="btn-primary">✓ Create Account</button>
                    <a href="{{ route('admin.show.crudSection') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>