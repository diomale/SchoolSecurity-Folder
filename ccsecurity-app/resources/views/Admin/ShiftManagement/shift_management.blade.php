<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management - CCSS Admin</title>
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
            <a href="{{ route('admin.show.crudSection') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Inside Users</span></a>
            <a href="{{ route('security.user.table.section') }}" class="nav-link"><span class="nav-icon">👮</span><span>Security Guards</span></a>
            <a href="{{ route('show.admin.outsider.list') }}" class="nav-link"><span class="nav-icon">👤</span><span>Outsider Management</span></a>
            <a href="{{ route('admin.visit.requests') }}" class="nav-link"><span class="nav-icon">📅</span><span>Visit Requests</span></a>
            <a href="{{ route('admin.connection.requests') }}" class="nav-link"><span class="nav-icon">👨‍👩‍👧</span><span>Connections</span></a>
            <a href="{{ route('admin.events.pending') }}" class="nav-link"><span class="nav-icon">🎉</span><span>Events</span></a>
            <a href="{{ route('admin.qr.status.management') }}" class="nav-link"><span class="nav-icon">📱</span><span>QR Management</span></a>
            <a href="{{ route('admin.shift.management') }}" class="nav-link active"><span class="nav-icon">🕐</span><span>Shift Management</span></a>
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
                <h1>Shift <span class="highlight">Management</span></h1>
                <p class="subtitle">Organize and assign security guard duty schedules</p>
            </div>
            <button type="button" onclick="openAssignShiftModal()" class="btn-primary">
                + Assign New Shift
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fade-in">⚠ {{ session('error') }}</div>
        @endif

        <div class="glass-card fade-in" style="animation-delay:0.05s; padding:16px 24px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <form method="GET" action="{{ route('admin.shift.management') }}" class="search-form">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" class="search-input" placeholder="Search guard name, date, or status..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-secondary btn-sm">Search</button>
                    @if(request('search'))
                        <a href="{{ route('admin.shift.management') }}" class="btn-clear btn-sm">✖ Clear</a>
                    @endif
                </form>

                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" class="btn-danger btn-sm" disabled>
                        🗑 Bulk Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">🕐 Current Shifts</h3>
            </div>

            <!-- Hidden Bulk Delete Form -->
            <form id="bulk-delete-form" action="{{ route('admin.shift.bulk-delete') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><input type="checkbox" id="select-all" class="custom-checkbox"></th>
                            <th>Guard</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" value="{{ $shift->id }}" class="shift-checkbox custom-checkbox">
                            </td>
                            <td class="user-name">
                                <div class="avatar-placeholder" style="background:linear-gradient(135deg, var(--info), var(--primary));">
                                    {{ substr($shift->securityGuardUser->first_name ?? 'N', 0, 1) }}
                                </div>
                                {{ $shift->securityGuardUser->first_name ?? 'N/A' }} {{ $shift->securityGuardUser->last_name ?? '' }}
                            </td>
                            <td class="date-cell">{{ $shift->shift_date->format('M d, Y') }}</td>
                            <td>
                                <span style="font-weight:600; color:var(--text-main);">{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</span>
                                <span style="color:var(--text-muted); margin:0 4px;">→</span>
                                <span style="font-weight:600; color:var(--text-main);">{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</span>
                            </td>
                            <td>
                                <span class="badge role-badge">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hrs
                                </span>
                            </td>
                            <td>
                                @if(strtolower($shift->status) === 'scheduled')
                                    <span class="badge status-pending">Scheduled</span>
                                @elseif(strtolower($shift->status) === 'completed')
                                    <span class="badge status-approved">Completed</span>
                                @else
                                    <span class="badge status-inactive">{{ ucfirst($shift->status) }}</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.guard.shifts', ['id' => $shift->security_guard_user_id, 'back_url' => url()->current()]) }}" class="btn-icon btn-view" title="View Guard Schedule">👁</a>
                                    <form action="{{ route('admin.shift.delete', $shift->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this shift?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Delete Shift">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">🕐</div>
                                    <h3>No shifts found</h3>
                                    <p>Try adjusting your search or assign a new shift.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shifts->hasPages())
                <div style="padding:16px 24px;"><div class="pagination-container">{{ $shifts->appends(request()->query())->links() }}</div></div>
            @endif
        </div>
    </main>
</div>

<!-- Assign Shift Modal -->
<div id="assignShiftModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>🕐 Assign New Shift</h3>
            <button type="button" class="close-modal" onclick="closeAssignShiftModal()">&times;</button>
        </div>
        <p class="modal-desc">Schedule a single shift or a recurring pattern for a security guard.</p>

        @if ($errors->any())
            <div class="alert alert-danger" style="padding:10px; font-size:0.85rem; margin-bottom:15px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.assign.shift') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Security Guard <span style="color:var(--danger)">*</span></label>
                    <select name="security_guard_user_id" required class="form-select">
                        <option value="">Select Guard</option>
                        @foreach($securityGuards as $guard)
                            <option value="{{ $guard->id }}" {{ old('security_guard_user_id') == $guard->id ? 'selected' : '' }}>
                                {{ $guard->first_name }} {{ $guard->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Shift Type <span style="color:var(--danger)">*</span></label>
                    <select id="recurring_type" name="recurring_type" required onchange="toggleRecurringOptions()" class="form-select">
                        <option value="single" {{ old('recurring_type') === 'single' ? 'selected' : '' }}>Single Day</option>
                        <option value="recurring" {{ old('recurring_type') === 'recurring' ? 'selected' : '' }}>Recurring</option>
                    </select>
                </div>
            </div>

            <!-- Single Day Option -->
            <div id="single_day_option" class="form-group">
                <label>Shift Date <span style="color:var(--danger)">*</span></label>
                <input type="date" id="shift_date" name="shift_date" min="{{ today()->format('Y-m-d') }}" value="{{ old('shift_date') }}" class="form-input">
            </div>

            <!-- Recurring Options -->
            <div id="recurring_options" style="display: none;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Start Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" id="recurring_start_date" name="shift_date_recurring" min="{{ today()->format('Y-m-d') }}" value="{{ old('shift_date') }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>End Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" id="recurring_end_date" name="recurring_end_date" min="{{ today()->format('Y-m-d') }}" value="{{ old('recurring_end_date') }}" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Repeat On <span style="color:var(--danger)">*</span></label>
                    <div style="display: flex; gap:12px; flex-wrap: wrap; background:var(--bg-main); padding:14px; border-radius:var(--radius-sm);">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                        <label style="display: flex; align-items: center; gap: 6px; font-size:0.88rem; cursor:pointer;">
                            <input type="checkbox" name="recurring_days[]" value="{{ $index }}" class="custom-checkbox"> {{ $day }}
                        </label>
                        @endforeach
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                        <button type="button" onclick="selectWeekdays()" class="btn-clear btn-sm" style="font-size:0.75rem;">Mon-Fri</button>
                        <button type="button" onclick="selectWeekend()" class="btn-clear btn-sm" style="font-size:0.75rem;">Sat-Sun</button>
                        <button type="button" onclick="clearDays()" class="btn-clear btn-sm" style="font-size:0.75rem;">Clear</button>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Start Time <span style="color:var(--danger)">*</span></label>
                    <input type="time" name="start_time" required class="form-input">
                </div>
                <div class="form-group">
                    <label>End Time <span style="color:var(--danger)">*</span></label>
                    <input type="time" name="end_time" required class="form-input">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeAssignShiftModal()">Cancel</button>
                <button type="submit" class="btn-primary">✓ Assign Shift</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignShiftModal() {
        const m = document.getElementById('assignShiftModal');
        m.style.display = 'flex'; setTimeout(() => m.classList.add('show'), 10);
    }
    function closeAssignShiftModal() {
        const m = document.getElementById('assignShiftModal');
        m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300);
    }

    function toggleRecurringOptions() {
        const type = document.getElementById('recurring_type').value;
        const single = document.getElementById('single_day_option');
        const recur = document.getElementById('recurring_options');

        if (type === 'single') {
            single.style.display = 'block'; recur.style.display = 'none';
            document.getElementById('recurring_end_date').disabled = true;
            document.getElementById('recurring_start_date').disabled = true;
            document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.disabled = true);
        } else {
            single.style.display = 'none'; recur.style.display = 'block';
            document.getElementById('recurring_end_date').disabled = false;
            document.getElementById('recurring_start_date').disabled = false;
            document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.disabled = false);
        }
    }

    function selectWeekdays() { document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.checked = ['1','2','3','4','5'].includes(cb.value)); }
    function selectWeekend() { document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.checked = ['0','6'].includes(cb.value)); }
    function clearDays() { document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => cb.checked = false); }

    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.shift-checkbox').forEach(cb => cb.checked = this.checked);
        toggleBulkBtn();
    });
    document.addEventListener('change', e => { if(e.target.classList.contains('shift-checkbox')) toggleBulkBtn(); });
    function toggleBulkBtn() {
        const count = document.querySelectorAll('.shift-checkbox:checked').length;
        document.getElementById('bulk-delete-btn').disabled = count === 0;
    }

    function submitBulkAction(formId, isDelete = false) {
        const ids = Array.from(document.querySelectorAll('.shift-checkbox:checked')).map(cb => cb.value);
        if (isDelete && !confirm(`Delete ${ids.length} selected shifts?`)) return;
        const form = document.getElementById(formId);
        form.querySelectorAll('input[name="shift_ids[]"]').forEach(el => el.remove());
        ids.forEach(id => {
            const inp = document.createElement('input'); inp.type='hidden'; inp.name='shift_ids[]'; inp.value=id; form.appendChild(inp);
        });
        form.submit();
    }

    window.onclick = e => {
        const m = document.getElementById('assignShiftModal');
        if (e.target === m) closeAssignShiftModal();
    };

    document.addEventListener('DOMContentLoaded', toggleRecurringOptions);
</script>
</body>
</html>