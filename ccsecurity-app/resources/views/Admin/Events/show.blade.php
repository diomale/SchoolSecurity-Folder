<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Review – {{ $event->event_name }} - CCSS Admin</title>
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
            <a href="{{ route('admin.events.pending') }}" class="nav-link active"><span class="nav-icon">🎉</span><span>Events</span></a>
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
                <h1 style="font-size:1.7rem;">{{ $event->event_name }}</h1>
                <p class="subtitle">Event review & approval</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('admin.events.pending') }}" class="btn-secondary">← Pending</a>
                <a href="{{ route('admin.events.all') }}" class="btn-secondary">📋 All Events</a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">
            <!-- Left column -->
            <div>
                <!-- Event Info -->
                <div class="glass-card fade-in" style="animation-delay:0.05s;">
                    <h3>🎉 Event Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item" style="grid-column:span 2;">
                            <div class="detail-label">Event Name</div>
                            <div class="detail-value">{{ $event->event_name }}</div>
                        </div>
                        <div class="detail-item" style="grid-column:span 2;">
                            <div class="detail-label">Description</div>
                            <div class="detail-value" style="font-weight:400; font-size:0.95rem;">{{ $event->event_description ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date</div>
                            <div class="detail-value">{{ $event->event_date->format('l, F d, Y') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Time</div>
                            <div class="detail-value">{{ $event->event_start_time->format('g:i A') }} – {{ $event->event_end_time->format('g:i A') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Registration Deadline</div>
                            <div class="detail-value">{{ $event->qr_request_deadline->format('M d, Y g:i A') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Participant Limit</div>
                            <div class="detail-value">{{ $event->alien_user_limit }}</div>
                        </div>
                    </div>
                </div>

                <!-- Organizer -->
                <div class="glass-card fade-in" style="animation-delay:0.1s;">
                    <h3>👤 Organizer</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Name</div>
                            <div class="detail-value">{{ $event->insideUser->fullname ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $event->insideUser->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Registrations -->
                @if($recentRegistrations->count() > 0)
                <div class="glass-card fade-in" style="animation-delay:0.15s; padding:0; overflow:hidden;">
                    <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                        <h3 style="margin:0; border:none; padding:0;">📋 Recent Registrations ({{ $recentRegistrations->count() }})</h3>
                    </div>
                    <div class="table-container" style="border-radius:0; border:none;">
                        <table class="modern-table">
                            <thead><tr><th>Name</th><th>Email</th><th style="font-size:0.78rem;">QR Code</th></tr></thead>
                            <tbody>
                                @foreach($recentRegistrations as $reg)
                                <tr>
                                    <td>{{ $reg->fullname }}</td>
                                    <td>{{ $reg->email }}</td>
                                    <td><code style="font-size:0.78rem; color:var(--text-muted);">{{ $reg->qr_code }}</code></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right column: Status + Actions -->
            <div>
                <!-- Current Status -->
                <div class="glass-card fade-in" style="animation-delay:0.05s;">
                    <h3>📌 Current Status</h3>
                    @if($event->status === 'pending')
                        <span class="badge status-pending" style="font-size:0.95rem; padding:8px 16px;">⏳ Pending Approval</span>
                    @elseif($event->status === 'approved')
                        <span class="badge status-approved" style="font-size:0.95rem; padding:8px 16px;">✅ Approved</span>
                    @elseif($event->status === 'rejected')
                        <span class="badge status-rejected" style="font-size:0.95rem; padding:8px 16px;">❌ Rejected</span>
                    @endif

                    @if($event->admin_remarks)
                        <div style="margin-top:16px; padding:14px; background:var(--bg-main); border-radius:var(--radius-sm);">
                            <div style="font-size:0.78rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Admin Remarks</div>
                            <p style="margin:0; font-size:0.93rem;">{{ $event->admin_remarks }}</p>
                        </div>
                    @endif

                    <div style="margin-top:16px; padding:14px; background:var(--bg-main); border-radius:var(--radius-sm);">
                        <div style="font-size:0.78rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Quick Stats</div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                            <span style="color:var(--text-muted); font-size:0.9rem;">Days Until Event</span>
                            <strong>{{ max(0, now()->diffInDays($event->event_date)) }}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-muted); font-size:0.9rem;">Submitted</span>
                            <strong>{{ $event->created_at->diffForHumans() }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Approval Actions -->
                @if($event->status === 'pending')
                <div class="glass-card fade-in" style="animation-delay:0.1s;">
                    <h3>⚡ Approval Actions</h3>

                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" style="margin-bottom:16px;">
                        @csrf
                        <div class="form-group">
                            <label>Admin Remarks <span style="color:var(--text-muted); font-weight:400;">(optional)</span></label>
                            <textarea name="admin_remarks" class="form-textarea" rows="3" placeholder="Add any notes or conditions..."></textarea>
                        </div>
                        <button type="submit" class="btn-success btn-block" onclick="return confirm('Approve this event?')">✅ Approve Event</button>
                    </form>

                    <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Rejection Reason <span style="color:var(--danger)">*</span></label>
                            <textarea name="admin_remarks" class="form-textarea" rows="3" required placeholder="Explain why this event is rejected..."></textarea>
                        </div>
                        <button type="submit" class="btn-danger btn-block" onclick="return confirm('Reject this event?')">❌ Reject Event</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
