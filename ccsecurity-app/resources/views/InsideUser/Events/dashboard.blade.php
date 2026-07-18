<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events Dashboard - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css', 'resources/css/InsideUser/insideuser_style_events.css'])
</head>
<body>
    <div class="dashboard-container">
        
        @include('InsideUser.partials.sidebar', ['activePage' => 'events'])

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">My <span class="highlight">Events</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage and track your custom events.</p>
                </div>
                <div class="header-right fade-in" style="animation-delay: 0.1s;">
                    @if($canCreateEvents)
                        <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
                    @endif
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
                                        <div style="font-weight: 600;">@if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('M d') }} – {{ $event->event_end_date->format('M d, Y') }}@else{{ $event->event_date->format('M d, Y') }}@endif</div>
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
                        <div class="empty-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <h4>No events yet</h4>
                        @if($canCreateEvents)
                            <p>Get started by creating your first event to invite outsiders.</p>
                            <br>
                            <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
                        @else
                            <p>You do not have permission to create events. Please contact an admin to request access.</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
