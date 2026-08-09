<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'events'])

    <main class="main-content">
        <!-- Header -->
        <div class="top-header fade-in">
            <div>
                <h1>All <span class="highlight">Events</span></h1>
                <p class="subtitle">View and manage all system events</p>
            </div>
            <a href="{{ route('admin.events.pending') }}" class="btn-warning">Pending Approvals</a>
        </div>

        <!-- Stats -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:14px; margin-bottom:24px;" class="fade-in">
            <div class="glass-card" style="margin:0; padding:16px 20px; text-align:center; border-top:3px solid var(--text-muted);">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Total</div>
                <div style="font-size:2rem; font-weight:800; margin-top:4px;">{{ $statistics['total'] }}</div>
            </div>
            <div class="glass-card" style="margin:0; padding:16px 20px; text-align:center; border-top:3px solid var(--warning);">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Pending</div>
                <div style="font-size:2rem; font-weight:800; margin-top:4px; color:var(--warning);">{{ $statistics['pending'] }}</div>
            </div>
            <div class="glass-card" style="margin:0; padding:16px 20px; text-align:center; border-top:3px solid var(--success);">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Approved</div>
                <div style="font-size:2rem; font-weight:800; margin-top:4px; color:var(--success);">{{ $statistics['approved'] }}</div>
            </div>
            <div class="glass-card" style="margin:0; padding:16px 20px; text-align:center; border-top:3px solid var(--danger);">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Rejected</div>
                <div style="font-size:2rem; font-weight:800; margin-top:4px; color:var(--danger);">{{ $statistics['rejected'] }}</div>
            </div>
            <div class="glass-card" style="margin:0; padding:16px 20px; text-align:center; border-top:3px solid var(--info);">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Completed</div>
                <div style="font-size:2rem; font-weight:800; margin-top:4px; color:var(--info);">{{ $statistics['completed'] }}</div>
            </div>
        </div>

        <!-- Filter -->
        <div class="glass-card fade-in" style="animation-delay:0.05s; padding:16px 24px; margin-bottom:20px;">
            <form action="{{ route('admin.events.all') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <div class="search-input-wrapper" style="flex:2; min-width:220px;">
                    <span class="search-icon"></span>
                    <input type="text" name="search" class="search-input" style="width:100%;" placeholder="Search events or organizers..." value="{{ request('search') }}">
                </div>
                <select name="status" class="form-select" style="flex:1; min-width:160px;">
                    <option value="">All Statuses</option>
                    <option value="pending"   {{ request('status') === 'pending'    ? 'selected' : '' }}>Pending</option>
                    <option value="approved"  {{ request('status') === 'approved'   ? 'selected' : '' }}>Approved</option>
                    <option value="rejected"  {{ request('status') === 'rejected'   ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ request('status') === 'completed'  ? 'selected' : '' }}>Completed</option>
                </select>
                <input type="date" name="date_from" class="form-input" style="flex:1; min-width:160px;" value="{{ request('date_from') }}">
                <button type="submit" class="btn-primary">Filter</button>
                @if(request('search') || request('status') || request('date_from'))
                    <a href="{{ route('admin.events.all') }}" class="btn-clear"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05);">
                <h3 style="margin:0; border:none; padding:0;">Events List</h3>
            </div>
            @if($events->count() > 0)
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Organizer</th>
                            <th>Date & Time</th>
                            <th>Registrations</th>
                            <th>Status</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $event->event_name }}</div>
                                <div style="font-size:0.82rem; color:var(--text-muted);">{{ Str::limit($event->event_description, 50) }}</div>
                            </td>
                            <td>
                                <div class="user-name">
                                    <span style="color: #000; font-weight: 600;">
                                        {{ $event->insideUser->fullname ?? '?' }}
                                    </span>
                                    {{ $event->insideUser->fullname ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="date-cell">
                                @if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('M d') }} – {{ $event->event_end_date->format('M d, Y') }}@else{{ $event->event_date->format('M d, Y') }}@endif<br>
                                <small>{{ $event->event_start_time->format('g:i A') }}</small>
                            </td>
                            <td>
                                <span style="font-weight:600;">{{ $event->registrations_count }}</span>
                                <span style="color:var(--text-muted);">/ {{ $event->alien_user_limit }}</span>
                            </td>
                            <td>
                                @if($event->status === 'pending')   <span class="badge status-pending">Pending</span>
                                @elseif($event->status === 'approved')  <span class="badge status-approved">Approved</span>
                                @elseif($event->status === 'rejected')  <span class="badge status-rejected">Rejected</span>
                                @elseif($event->status === 'cancelled') <span class="badge" style="background:rgba(0,0,0,0.06);color:var(--text-muted);">Cancelled</span>
                                @elseif($event->status === 'completed') <span class="badge" style="background:var(--info-light);color:var(--info);">Completed</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.events.show', $event->id) }}" class="btn-icon btn-view" title="View"></a>
                                    @if($event->status === 'approved')
                                        <form action="{{ route('admin.events.mark-completed', $event->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark as completed?')">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-success" title="Mark Complete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px 24px;">
                <div class="pagination-container">{{ $events->links() }}</div>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"></div>
                <h3>No Events Found</h3>
                <p>No events match your current filters.</p>
            </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
