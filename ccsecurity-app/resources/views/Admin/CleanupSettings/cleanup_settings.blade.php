<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanup Settings - CCSS Admin</title>
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
            <a href="{{ route('admin.shift.management') }}" class="nav-link"><span class="nav-icon">🕐</span><span>Shift Management</span></a>
            <a href="{{ route('admin.cleanup.settings') }}" class="nav-link active"><span class="nav-icon">🗑️</span><span>Cleanup Settings</span></a>
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
                <h1>Auto-Delete <span class="highlight">Cleanup Settings</span></h1>
                <p class="subtitle">Manage scheduled data cleanup and retention policies</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-in">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger fade-in">⚠ {{ session('error') }}</div>
        @endif

        <!-- Global Status Card -->
        <div class="glass-card fade-in" style="animation-delay:0.05s;">
            <h3>⚙️ Global Auto-Delete Status</h3>
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                <div>
                    <div style="font-size:1rem; font-weight:600; color:var(--text-main); margin-bottom:4px;">Master Auto-Delete Switch</div>
                    <div style="font-size:0.9rem; color:var(--text-muted);">
                        Currently:
                        @if($globalSettings->auto_delete_enabled)
                            <strong style="color:var(--success);">ENABLED</strong>
                        @else
                            <strong style="color:var(--danger);">DISABLED</strong>
                        @endif
                    </div>
                </div>
                <button onclick="openPasswordModal('toggle-global')" class="btn-primary">⚙ Toggle Global Setting</button>
            </div>
            <div class="alert alert-info" style="margin:0;">
                ℹ <strong>Note:</strong> The global switch controls whether scheduled cleanup runs automatically.
                Individual table settings control which tables are cleaned and their retention periods.
                Scheduled cleanup runs <strong>daily at midnight</strong>.
            </div>
        </div>

        <!-- Table-Specific Settings Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px,1fr)); gap:16px; margin-bottom:24px;" class="fade-in" style="animation-delay:0.1s;">
            @foreach($tableSettings as $tableName => $tableData)
                @php
                    $settings = $tableData['settings'];
                    $label = $tableData['label'];
                    $tableStats = $stats[$tableName];
                @endphp
                <div class="glass-card" style="margin:0; padding:22px;">
                    <h3 style="font-size:1rem; margin-bottom:16px;">🗂 {{ $label }}</h3>

                    <!-- Stats -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                        <div style="background:var(--bg-main); border-radius:var(--radius-sm); padding:12px; text-align:center;">
                            <div style="font-size:0.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Records</div>
                            <div style="font-size:1.6rem; font-weight:800;">{{ $tableStats['total'] }}</div>
                        </div>
                        <div style="background:var(--orange-light); border-radius:var(--radius-sm); padding:12px; text-align:center;">
                            <div style="font-size:0.72rem; color:var(--orange); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">&gt;30 Days Old</div>
                            <div style="font-size:1.6rem; font-weight:800; color:var(--orange);">{{ $tableStats['older_than_30_days'] }}</div>
                        </div>
                    </div>

                    <!-- Settings Summary -->
                    <div style="background:var(--bg-main); border-radius:var(--radius-sm); padding:12px; margin-bottom:14px; font-size:0.9rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span style="color:var(--text-muted);">Auto-Delete</span>
                            @if($settings->auto_delete_enabled)
                                <strong style="color:var(--success);">Enabled</strong>
                            @else
                                <strong style="color:var(--danger);">Disabled</strong>
                            @endif
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span style="color:var(--text-muted);">Retention Period</span>
                            <strong style="color:var(--info);">
                                {{ $settings->retention_days }} days
                                @if($settings->retention_days == 0)<span style="color:var(--warning); font-size:0.8rem;"> (all)</span>@endif
                            </strong>
                        </div>
                        <div style="font-size:0.78rem; color:var(--text-muted); margin-top:6px; border-top:1px solid rgba(0,0,0,0.05); padding-top:6px;">
                            Last cleanup: {{ $settings->last_cleanup_date ? $settings->last_cleanup_date->format('M d, Y h:i A') : 'Never' }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex; gap:8px;">
                        <button onclick="openEditModal('{{ $tableName }}', {{ $settings->auto_delete_enabled ? 1 : 0 }}, {{ $settings->retention_days }})"
                            class="btn-info btn-sm" style="flex:1;">✎ Edit Settings</button>
                        <button onclick="openRunModal('{{ $tableName }}', {{ $settings->retention_days }})"
                            class="btn-danger btn-sm" style="flex:1;">▶ Run Now</button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Scheduled Cleanup Info -->
        <div class="glass-card fade-in" style="animation-delay:0.15s;">
            <h3>🕐 Scheduled Cleanup Info</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:12px;">
                <div class="detail-item">
                    <div class="detail-label">Schedule</div>
                    <div class="detail-value">Every day at 12:00 AM</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Global Auto-Delete</div>
                    <div class="detail-value">
                        @if($globalSettings->auto_delete_enabled)
                            <span class="badge status-active">Enabled</span>
                        @else
                            <span class="badge status-inactive">Disabled</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="alert alert-warning" style="margin-top:16px; margin-bottom:0;">
                ⚠ <strong>Production Setup:</strong> Windows Task Scheduler runs daily. Each table uses its own retention setting.
                Setting retention to 0 will delete <strong>all records</strong> from that table.
            </div>
        </div>
    </main>
</div>

<!-- Password Modal for Global Toggle -->
<div id="passwordModal-toggle-global" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>⚙️ Confirm Global Toggle</h3>
            <button type="button" class="close-modal" onclick="closePasswordModal('toggle-global')">&times;</button>
        </div>
        <p class="modal-desc">Enter your admin password to toggle the global auto-delete setting.</p>
        <form action="{{ route('admin.cleanup.toggle-global') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Enter your password">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closePasswordModal('toggle-global')">Cancel</button>
                <button type="submit" class="btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Settings Modal -->
<div id="editSettingsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>✎ Edit Cleanup Settings</h3>
            <button type="button" class="close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <p class="modal-desc">Enter your admin password to update retention settings.</p>
        <form action="{{ route('admin.cleanup.update-table') }}" method="POST">
            @csrf
            <input type="hidden" name="table_name" id="edit-table-name">
            <div class="form-group">
                <label>Auto-Delete</label>
                <select name="auto_delete_enabled" id="edit-auto-delete" class="form-select">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>
            <div class="form-group">
                <label>Retention Period (days)</label>
                <input type="number" name="retention_days" id="edit-retention-days" min="0" max="365" class="form-input" required>
                <span style="font-size:0.8rem; color:var(--text-muted); margin-top:4px; display:block;">0 = Delete ALL records (no retention)</span>
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Enter your password">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Run Cleanup Modal -->
<div id="runCleanupModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>▶ Run Manual Cleanup</h3>
            <button type="button" class="close-modal" onclick="closeRunModal()">&times;</button>
        </div>
        <p class="modal-desc">Enter your admin password to run cleanup now.</p>
        <div class="alert alert-warning" style="margin-bottom:16px;">
            ⚠ This will permanently delete records <span id="run-retention-text"></span>
        </div>
        <form action="{{ route('admin.cleanup.run-now') }}" method="POST">
            @csrf
            <input type="hidden" name="table_name" id="run-table-name">
            <input type="hidden" name="retention_days" id="run-retention-days">
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Enter your password">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeRunModal()">Cancel</button>
                <button type="submit" class="btn-danger">▶ Run Cleanup</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal(type) {
        const m = document.getElementById('passwordModal-' + type);
        m.style.display = 'flex'; setTimeout(() => m.classList.add('show'), 10);
    }
    function closePasswordModal(type) {
        const m = document.getElementById('passwordModal-' + type);
        m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300);
    }
    function openEditModal(tableName, autoDelete, retentionDays) {
        document.getElementById('edit-table-name').value = tableName;
        document.getElementById('edit-auto-delete').value = autoDelete;
        document.getElementById('edit-retention-days').value = retentionDays;
        const m = document.getElementById('editSettingsModal');
        m.style.display = 'flex'; setTimeout(() => m.classList.add('show'), 10);
    }
    function closeEditModal() {
        const m = document.getElementById('editSettingsModal');
        m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300);
    }
    function openRunModal(tableName, retentionDays) {
        document.getElementById('run-table-name').value = tableName;
        document.getElementById('run-retention-days').value = retentionDays;
        document.getElementById('run-retention-text').textContent = retentionDays == 0
            ? 'completely (ALL records)' : 'older than ' + retentionDays + ' days';
        const m = document.getElementById('runCleanupModal');
        m.style.display = 'flex'; setTimeout(() => m.classList.add('show'), 10);
    }
    function closeRunModal() {
        const m = document.getElementById('runCleanupModal');
        m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300);
    }
    window.onclick = e => {
        ['editSettingsModal','runCleanupModal','passwordModal-toggle-global'].forEach(id => {
            const m = document.getElementById(id);
            if (e.target === m) { m.classList.remove('show'); setTimeout(() => m.style.display='none', 300); }
        });
    };
</script>
</body>
</html>
