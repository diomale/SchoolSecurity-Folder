<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Security Guard - CCSS Admin</title>
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
                <h1>Edit <span class="highlight">Security Guard</span></h1>
                <p class="subtitle">Updating {{ $security_guard_user->first_name }} {{ $security_guard_user->last_name }}</p>
            </div>
            <a href="{{ $backUrl }}" class="btn-secondary">← Back</a>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; max-width:680px;">
            <h3>Edit Guard Information</h3>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px;">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('security.guard.user.update', $security_guard_user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="first_name" class="form-input" value="{{ old('first_name', $security_guard_user->first_name) }}" required>
                        @error('first_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Last Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="last_name" class="form-input" value="{{ old('last_name', $security_guard_user->last_name) }}" required>
                        @error('last_name')<span class="error-text">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $security_guard_user->email) }}" required>
                    @error('email')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>New Password <span style="color:var(--text-muted); font-weight:400;">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-input" placeholder="Set new password">
                    @error('password')<span class="error-text">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Account Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', $security_guard_user->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $security_guard_user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="{{ $backUrl }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
