<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Status Management - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'qr_management'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>QR <span class="highlight">Status Management</span></h1>
                <p class="subtitle">Activate or deactivate QR codes for students and staff</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">{{ session('success') }}</div>
        @endif

        <!-- Bulk Actions Bar -->
        <div class="glass-card fade-in" style="animation-delay:0.05s; padding:16px 24px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <!-- Search -->
                <form method="GET" action="{{ route('admin.qr.status.management') }}" class="search-form">
                    <div class="search-input-wrapper">
                        <span class="search-icon"></span>
                        <input type="text" name="search" class="search-input" placeholder="Search by name, email, or QR value..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-secondary btn-sm">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.qr.status.management') }}" class="btn-clear btn-sm">✖ Clear</a>
                    @endif
                </form>

                <!-- Bulk toggle -->
                <div style="display:flex; gap:10px; align-items:center;">
                    <form id="bulk-toggle-form" method="POST" action="{{ route('admin.qr.status.bulk.toggle') }}" style="display:flex; gap:8px; align-items:center;">
                        @csrf
                        <select name="new_status" class="form-select" style="width:140px; padding:9px 12px;">
                            <option value="active">Activate</option>
                            <option value="inactive">Deactivate</option>
                        </select>
                        <button type="button" onclick="submitBulkAction('bulk-toggle-form')" id="bulk-toggle-btn" class="btn-info btn-sm" disabled>Apply to Selected</button>
                    </form>
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.inside-user.bulk-delete') }}">
                        @csrf @method('DELETE')
                        <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" class="btn-danger btn-sm" disabled>Bulk Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:14px; margin-bottom:20px;" class="fade-in">
            <div class="glass-card" style="margin:0; padding:16px 20px; display:flex; align-items:center; gap:12px; border-left:4px solid var(--primary);">
                <div style="font-size:1.8rem;"></div>
                <div><div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Students</div>
                <div style="font-size:1.6rem; font-weight:800;">{{ $students->total() }}</div></div>
            </div>
            <div class="glass-card" style="margin:0; padding:16px 20px; display:flex; align-items:center; gap:12px; border-left:4px solid var(--info);">
                <div style="font-size:1.8rem;"></div>
                <div><div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Staff</div>
                <div style="font-size:1.6rem; font-weight:800;">{{ $staff->total() }}</div></div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden; margin-bottom:24px;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">Students ({{ $students->total() }})</h3>
            </div>
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" class="select-all custom-checkbox" data-target="student-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>QR Status</th>
                            <th class="actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox student-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                <span style="color: #000; font-weight: 600;">{{ $user->fullname ?? $user->first_name }}</span>
                                {{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(strtolower($user->qr_status) === 'active')
                                    <span class="badge status-active">Active</span>
                                @else
                                    <span class="badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <form action="{{ route('admin.qr.status.toggle', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    @if(strtolower($user->qr_status) === 'active')
                                        <button type="submit" class="btn-warning btn-sm" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">Deactivate</button>
                                    @else
                                        <button type="submit" class="btn-success btn-sm" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">Activate</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"></div><h3>No students found</h3></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div style="padding:16px 24px;"><div class="pagination-container">{{ $students->appends(request()->query())->links() }}</div></div>
            @endif
        </div>

        <!-- Staff Table -->
        <div class="glass-card fade-in" style="animation-delay:0.15s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">Staff ({{ $staff->total() }})</h3>
            </div>
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" class="select-all custom-checkbox" data-target="staff-checkbox"></th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>QR Status</th>
                            <th class="actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox staff-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                <span style="color: #000; font-weight: 600;">{{ $user->fullname ?? $user->first_name }}</span>
                                {{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(strtolower($user->qr_status) === 'active')
                                    <span class="badge status-active">Active</span>
                                @else
                                    <span class="badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <form action="{{ route('admin.qr.status.toggle', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    @if(strtolower($user->qr_status) === 'active')
                                        <button type="submit" class="btn-warning btn-sm" onclick="return confirm('Deactivate QR for {{ $user->fullname ?? $user->first_name }}?')">Deactivate</button>
                                    @else
                                        <button type="submit" class="btn-success btn-sm" onclick="return confirm('Activate QR for {{ $user->fullname ?? $user->first_name }}?')">Activate</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"></div><h3>No staff found</h3></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($staff->hasPages())
                <div style="padding:16px 24px;"><div class="pagination-container">{{ $staff->appends(request()->query())->links() }}</div></div>
            @endif
        </div>
    </main>
</div>

<script>
    document.querySelectorAll('.select-all').forEach(selectAll => {
        selectAll.addEventListener('change', function() {
            const targetClass = this.getAttribute('data-target');
            document.querySelectorAll('.' + targetClass).forEach(cb => cb.checked = this.checked);
            toggleBulkButtons();
        });
    });
    document.addEventListener('change', e => { if (e.target.classList.contains('user-checkbox')) toggleBulkButtons(); });
    function toggleBulkButtons() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        document.querySelectorAll('[id^="bulk-"]').forEach(btn => btn.disabled = count === 0);
    }
    function submitBulkAction(formId, isDelete = false) {
        const ids = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        if (isDelete && !confirm(`Delete ${ids.length} selected users?`)) return;
        const form = document.getElementById(formId);
        form.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());
        ids.forEach(id => { const inp = document.createElement('input'); inp.type='hidden'; inp.name='user_ids[]'; inp.value=id; form.appendChild(inp); });
        form.submit();
    }
</script>
</body>
</html>
