<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events Dashboard - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUserStyleFolder/insideuser_dashboard_style.css', 'resources/css/InsideUserStyleFolder/insideuser_style_events.css'])
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

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('insideuser.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">My <span class="highlight">Events</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage and track your custom events.</p>
                </div>
                <div class="header-right fade-in" style="animation-delay: 0.1s;">
                     <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Events</h3>
                        <div class="value" style="color: var(--primary);">{{ $totalEvents }}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Approval</h3>
                        <div class="value" style="color: var(--warning);">{{ $pendingEvents }}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Approved Events</h3>
                        <div class="value" style="color: var(--success);">{{ $approvedEvents }}</div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Registrations</h3>
                        <div class="value" style="color: var(--purple);">{{ $totalRegistrations }}</div>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="glass-card">
                    <h3 class="section-title mb-4">Your Events</h3>

                    @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date & Time</th>
                                    <th>Registration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-main);">{{ $event->event_name }}</div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">{{ Str::limit($event->event_description, 50) }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $event->event_date->format('M d, Y') }}</div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $event->registrations_count }} / {{ $event->alien_user_limit }}</div>
                                    </td>
                                    <td>
                                        @if($event->status === 'pending')
                                            <span class="badge badge-yellow">Pending</span>
                                        @elseif($event->status === 'approved')
                                            <span class="badge badge-green">Approved</span>
                                        @elseif($event->status === 'completed')
                                            <span class="badge badge-blue">Completed</span>
                                        @elseif($event->status === 'cancelled')
                                            <span class="badge badge-red">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('insideuser.events.show', $event->id) }}" class="btn btn-sm btn-secondary" style="margin-right: 5px;">View</a>
                                        @if($event->status === 'approved')
                                            <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-sm btn-primary">Registrations</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($events->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $events->links() }}
                    </div>
                    @endif

                    @else
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <h4>No events yet</h4>
                        <p>Get started by creating your first event to invite outsiders.</p>
                        <br>
                        <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
