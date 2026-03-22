<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Management - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/AdminStyleFolder/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">CCSS</div>
            <div class="sidebar-brand">
                <strong>Columban College</strong>
                <span>Admin Portal</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Inside Users</span></a>
            <a href="{{ route('security.user.table.section') }}" class="nav-link active"><span class="nav-icon">👮</span><span>Security Guards</span></a>
            <a href="{{ route('show.admin.outsider.list') }}" class="nav-link"><span class="nav-icon">👤</span><span>Outsider Management</span></a>
            <a href="{{ route('admin.visit.requests') }}" class="nav-link"><span class="nav-icon">📅</span><span>Visit Requests</span></a>
            <a href="{{ route('admin.connection.requests') }}" class="nav-link"><span class="nav-icon">👨‍👩‍👧</span><span>Connections</span></a>
            <a href="{{ route('admin.events.pending') }}" class="nav-link"><span class="nav-icon">🎉</span><span>Events</span></a>
            <a href="{{ route('admin.qr.status.management') }}" class="nav-link"><span class="nav-icon">📱</span><span>QR Management</span></a>
            <a href="{{ route('admin.shift.management') }}" class="nav-link"><span class="nav-icon">🕐</span><span>Shift Management</span></a>
            <a href="{{ route('admin.cleanup.settings') }}" class="nav-link"><span class="nav-icon">🗑️</span><span>Cleanup Settings</span></a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn"><span class="nav-icon">🚪</span><span>Logout</span></button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Security Guard <span class="highlight">Management</span></h1>
                <p class="subtitle">Add, edit, or remove security personnel accounts</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('admin.shift.management') }}" class="btn-info">🕐 Manage Shifts</a>
                <a href="{{ route('security.user.add.section') }}" class="btn-primary">+ Add Guard</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fade-in">⚠ {{ session('error') }}</div>
        @endif

        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <!-- Toolbar -->
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <button type="button" onclick="openPasswordModal('bulk-delete-form')"
                    id="bulk-delete-btn" class="btn-danger btn-sm" disabled>
                    🗑 Bulk Delete
                </button>
                <form action="{{ route('security.user.table.section') }}" method="GET" class="search-form">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" class="search-input" placeholder="Search guards by name or email..."
                            value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-secondary btn-sm">Search</button>
                    @if(request('search'))
                        <a href="{{ route('security.user.table.section') }}" class="btn-clear btn-sm">✖ Clear</a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($security_guard_users as $security_guard_user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" value="{{ $security_guard_user->id }}" class="user-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                <div class="avatar-placeholder" style="background: linear-gradient(135deg, var(--info), #60a5fa);">
                                    {{ substr($security_guard_user->first_name, 0, 1) }}
                                </div>
                                {{ $security_guard_user->first_name }} {{ $security_guard_user->last_name }}
                            </td>
                            <td>{{ $security_guard_user->email }}</td>
                            <td class="date-cell">{{ \Carbon\Carbon::parse($security_guard_user->created_at)->format('M d, Y') }}</td>
                            <td class="date-cell">{{ \Carbon\Carbon::parse($security_guard_user->updated_at)->format('M d, Y') }}</td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.guard.shifts', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}"
                                        class="btn-icon btn-info" title="View Shifts">🕐</a>
                                    <a href="{{ route('security.guard.user.details', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}"
                                        class="btn-icon btn-view" title="View">👁</a>
                                    <a href="{{ route('security.guard.user.edit', ['id' => $security_guard_user->id, 'back_url' => url()->current()]) }}"
                                        class="btn-icon btn-edit" title="Edit">✎</a>
                                    <button type="button" onclick="openPasswordModal('delete-form-{{ $security_guard_user->id }}')"
                                        class="btn-icon btn-delete" title="Delete">🗑</button>
                                    <form id="delete-form-{{ $security_guard_user->id }}"
                                        action="{{ route('security.guard.user.delete', $security_guard_user->id) }}"
                                        method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">👮</div>
                                    <h3>No Security Guards Found</h3>
                                    <p>No staff accounts match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 24px;">
                <div class="pagination-container">
                    {{ $security_guard_users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Hidden Bulk Delete Form -->
<form id="bulk-delete-form" action="{{ route('admin.security.bulk-delete') }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<!-- Password Confirmation Modal -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🔒 Confirm Your Identity</h3>
            <button type="button" class="close-modal" onclick="closePasswordModal()">&times;</button>
        </div>
        <p class="modal-desc">Enter your admin password to authorize this action.</p>
        <form id="passwordConfirmForm" method="POST">
            @csrf @method('DELETE')
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" id="admin_password" name="admin_password" class="form-input" placeholder="Enter password" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
                <button type="submit" class="btn-danger">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal(formId) {
        const sourceForm = document.getElementById(formId);
        const targetForm = document.getElementById('passwordConfirmForm');
        targetForm.action = sourceForm.action;
        targetForm.querySelectorAll('input[type="hidden"]').forEach(i => { if (i.name !== '_token' && i.name !== '_method') i.remove(); });
        if (formId === 'bulk-delete-form') {
            document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
                const h = document.createElement('input'); h.type='hidden'; h.name='user_ids[]'; h.value=cb.value; targetForm.appendChild(h);
            });
        }
        const modal = document.getElementById('passwordModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closePasswordModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }
    window.onclick = e => { if (e.target === document.getElementById('passwordModal')) closePasswordModal(); };
    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
        toggleBulk();
    });
    document.addEventListener('change', e => { if (e.target.classList.contains('user-checkbox')) toggleBulk(); });
    function toggleBulk() {
        document.getElementById('bulk-delete-btn').disabled = document.querySelectorAll('.user-checkbox:checked').length === 0;
    }
</script>
</body>
</html>
