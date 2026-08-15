<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Privileges - Admin - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'event_privileges'])

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1 class="fade-in">Event <span class="highlight">Privileges</span></h1>
                <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage which inside users can create events</p>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success fade-in">
                <div class="alert-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error fade-in">
                <div class="alert-icon">!</div>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid fade-in" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $totalUsers }}</span>
                    <span class="stat-label">Total Inside Users</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value text-success">{{ $grantedUsers }}</span>
                    <span class="stat-label">Privilege Granted</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value text-warning">{{ $deniedUsers }}</span>
                    <span class="stat-label">No Privilege</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card fade-in" style="animation-delay: 0.3s; margin-top: 20px;">
            <form method="GET" class="search-form-flex" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <div class="search-input-wrapper" style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" placeholder="Search by name, email, or role..." value="{{ request('search') }}" style="width: 100%; padding: 10px 15px; border: 1px solid rgba(0,0,0,0.1); border-radius: var(--radius-sm); font-family: 'Outfit', sans-serif;">
                </div>
                <select name="status" style="padding: 10px 15px; border: 1px solid rgba(0,0,0,0.1); border-radius: var(--radius-sm); font-family: 'Outfit', sans-serif; background: white;">
                    <option value="">All Status</option>
                    <option value="granted" {{ request('status') === 'granted' ? 'selected' : '' }}>Privilege Granted</option>
                    <option value="denied" {{ request('status') === 'denied' ? 'selected' : '' }}>No Privilege</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.event-privileges.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        <!-- Bulk Actions -->
        <form id="bulk-action-form" method="POST" action="{{ route('admin.event-privileges.bulk-toggle') }}">
            @csrf
            <input type="hidden" name="action" id="bulk-action-value" value="">
            <div class="glass-card fade-in" style="animation-delay: 0.35s; margin-top: 15px; display: none;" id="bulk-actions-bar">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-weight: 600; color: var(--text-main);" id="selected-count">0 selected</span>
                    <button type="submit" name="action" value="grant" class="btn btn-primary" onclick="document.getElementById('bulk-action-value').value='grant'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Grant Privilege
                    </button>
                    <button type="submit" name="action" value="revoke" class="btn btn-secondary" onclick="document.getElementById('bulk-action-value').value='revoke'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        Revoke Privilege
                    </button>
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <div class="glass-card fade-in" style="animation-delay: 0.4s; margin-top: 15px;">
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Event Privilege</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td><input type="checkbox" value="{{ $user->id }}" class="user-checkbox custom-checkbox"></td>
                            <td>
                                <div class="user-cell">
                                    <span class="full-name">{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</span>
                                </div>
                            </td>
                            <td><span style="color: var(--text-muted); font-size: 0.95rem;">{{ $user->email }}</span></td>
                            <td><span class="badge badge-outline">{{ ucfirst($user->role) }}</span></td>
                            <td>
                                @if(strtolower($user->status) === 'active' || $user->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-outline">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($user->can_create_events)
                                    <span class="badge badge-success">Granted</span>
                                @else
                                    <span class="badge badge-outline">No Privilege</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.event-privileges.toggle', $user->id) }}" style="display: inline;">
                                    @csrf
                                    @if($user->can_create_events)
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Revoke event creation privilege from {{ $user->fullname ?? $user->first_name }}?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                            Revoke
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Grant event creation privilege to {{ $user->fullname ?? $user->first_name }}?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            Grant
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">No inside users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div style="margin-top: 20px;">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bulkBar = document.getElementById('bulk-actions-bar');
    const countSpan = document.getElementById('selected-count');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        if (checked.length > 0) {
            bulkBar.style.display = 'block';
            countSpan.textContent = checked.length + ' selected';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });
});
</script>
</body>
</html>
