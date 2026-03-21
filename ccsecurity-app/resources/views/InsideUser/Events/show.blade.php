<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUserStyleFolder/insideuser_dashboard_style.css', 'resources/css/InsideUserStyleFolder/insideuser_style_events.css'])
    <style>
        .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 1024px) { .grid-2 { grid-template-columns: 1fr; } }
        .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed rgba(0,0,0,0.05); }
        .info-row:last-child { border-bottom: none; }
        .qr-container { background: white; border: 2px dashed rgba(0,0,0,0.1); padding: 20px; border-radius: var(--radius-md); text-align: center; margin: 20px 0; }
        .qr-container svg { display: inline-block; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('insideuser.dashboard') }}" class="nav-link">
                    <span class="nav-icon">📊</span> Overview
                </a>
                <a href="{{ route('insideuser.profile.show') }}" class="nav-link">
                    <span class="nav-icon">👤</span> Profile
                </a>
                <a href="{{ route('insideuser.events.dashboard') }}" class="nav-link active">
                    <span class="nav-icon">🎉</span> My Events
                </a>
                <a href="{{ route('insideuser.connection.requests') }}" class="nav-link">
                    <span class="nav-icon">🤝</span> Connection Requests
                </a>
                <a href="{{ route('insideuser.connected.parents') }}" class="nav-link">
                    <span class="nav-icon">👨‍👩‍👧</span> Connected Parents
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header" style="margin-bottom: 30px;">
                <div class="header-left">
                    <a href="{{ route('insideuser.events.dashboard') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.95rem; display: inline-block; margin-bottom: 15px;">&larr; Back to Events</a>
                    <h1 class="fade-in">{{ $event->event_name }}</h1>
                </div>
                <div class="header-right fade-in" style="animation-delay: 0.1s;">
                    @if($event->status === 'approved')
                        <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-success" style="margin-right: 10px;">Manage Registrations</a>
                        <a href="{{ route('insideuser.events.edit', $event->id) }}" class="btn btn-primary">Edit Event</a>
                    @endif
                </div>
            </header>

            <div class="grid-2 fade-in" style="animation-delay: 0.2s;">
                <!-- Left Column -->
                <div class="left-col" style="display: flex; flex-direction: column; gap: 30px;">
                    <!-- Event Info -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Event Information</h2>
                        <div class="info-row"><span style="color: var(--text-muted);">Event Name</span><strong style="color: var(--text-main);">{{ $event->event_name }}</strong></div>
                        <div class="info-row"><span style="color: var(--text-muted);">Description</span><span style="color: var(--text-main); text-align: right; max-width: 60%;">{{ $event->event_description ?? 'No description' }}</span></div>
                        <div class="info-row"><span style="color: var(--text-muted);">Date</span><strong style="color: var(--text-main);">{{ $event->event_date->format('l, F d, Y') }}</strong></div>
                        <div class="info-row"><span style="color: var(--text-muted);">Time</span><strong style="color: var(--text-main);">{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</strong></div>
                        <div class="info-row"><span style="color: var(--text-muted);">Registration Deadline</span><strong style="color: var(--text-main);">{{ $event->qr_request_deadline->format('M d, Y g:i A') }}</strong></div>
                    </div>

                    <!-- Event Status -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Event Status</h2>
                        <div style="margin-bottom: 15px;">
                            @if($event->status === 'pending')
                                <span class="badge badge-yellow" style="font-size: 1rem; padding: 8px 16px;">Pending Approval</span>
                            @elseif($event->status === 'approved')
                                <span class="badge badge-green" style="font-size: 1rem; padding: 8px 16px;">Approved</span>
                            @elseif($event->status === 'completed')
                                <span class="badge badge-blue" style="font-size: 1rem; padding: 8px 16px;">Completed</span>
                            @elseif($event->status === 'cancelled')
                                <span class="badge badge-red" style="font-size: 1rem; padding: 8px 16px;">Cancelled</span>
                            @endif
                        </div>
                        
                        @if($event->admin_remarks)
                            <div class="alert-info-box" style="margin-bottom: 0;">
                                <strong>Admin Remarks:</strong><br><span style="margin-top:5px; display:inline-block;">{{ $event->admin_remarks }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Registrations -->
                    @if($event->status === 'approved' && $registrations->count() > 0)
                    <div class="glass-card">
                        <div class="flex-between mb-4">
                            <h2 class="section-title" style="margin: 0; padding: 0; border: none;">Recent Registrations</h2>
                            <a href="{{ route('insideuser.events.registrations', $event->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">View All &rarr;</a>
                        </div>
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr><th>Name</th><th>Email</th><th>QR Code</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations->take(5) as $reg)
                                    <tr>
                                        <td><strong>{{ $reg->fullname }}</strong></td>
                                        <td>{{ $reg->email }}</td>
                                        <td><code style="font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px;">{{ $reg->qr_code }}</code></td>
                                        <td><span class="badge badge-green">{{ ucfirst($reg->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="right-col" style="display: flex; flex-direction: column; gap: 30px;">
                    <!-- Quick Stats -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Quick Stats</h2>
                        <div class="stats-grid" style="grid-template-columns: 1fr; gap: 15px; margin-bottom: 0;">
                            <div class="stat-card">
                                <h3 style="margin-bottom: 5px;">Registered</h3>
                                <div class="value" style="color: var(--info);">{{ $event->registrations_count }}</div>
                            </div>
                            <div class="stat-card">
                                <h3 style="margin-bottom: 5px;">Available Slots</h3>
                                <div class="value" style="color: var(--success);">{{ $event->alien_user_limit - $event->registrations_count }}</div>
                            </div>
                            <div class="stat-card">
                                <h3 style="margin-bottom: 5px;">Total Limit</h3>
                                <div class="value" style="color: var(--purple);">{{ $event->alien_user_limit }}</div>
                            </div>
                        </div>
                    </div>

                    @if($event->status === 'approved')
                    <!-- Share Event -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Share Event</h2>
                        <div class="qr-container">
                            {!! QrCode::size(160)->generate(route('public.event.register', ['code' => $event->id])) !!}
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-muted); text-align: center; margin-bottom: 10px;">Scan to register for this event</p>
                        <p style="font-size: 0.75rem; color: var(--text-light); text-align: center; word-break: break-all;">
                            {{ route('public.event.register', ['code' => $event->id]) }}
                        </p>
                    </div>

                    <!-- Approvals -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Registration Approvals</h2>
                        @php
                            $pendingCount = $event->pendingApprovals()->count() ?? 0;
                        @endphp
                        @if($pendingCount > 0)
                            <a href="{{ route('insideuser.events.approvals.pending', $event->id) }}" class="btn btn-primary" style="width: 100%; justify-content:center; margin-bottom: 15px;">
                                View Pending Approvals ({{ $pendingCount }})
                            </a>
                        @endif
                        <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-success" style="width: 100%; justify-content:center;">
                            Manage All Registrations
                        </a>
                    </div>
                    @endif

                    @if($event->status !== 'completed' && $event->status !== 'cancelled')
                    <!-- Actions -->
                    <div class="glass-card">
                        <h2 class="section-title mb-4">Danger Zone</h2>
                        @if($event->status === 'pending')
                            <button disabled class="btn" style="width: 100%; justify-content:center; background: rgba(239, 68, 68, 0.5); color:white; cursor:not-allowed;">Cancel Event (Pending)</button>
                        @else
                            <form action="{{ route('insideuser.events.cancel', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="width: 100%; justify-content:center; background: var(--danger); color: white;">Cancel Event</button>
                            </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
