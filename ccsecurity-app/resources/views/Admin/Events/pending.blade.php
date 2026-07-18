<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Events - CCSS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/Admin/admin_style_shared.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    @include('Admin.partials.sidebar', ['activePage' => 'events'])

    <main class="main-content">
        <div class="top-header fade-in">
            <div>
                <h1>Pending <span class="highlight">Event Approvals</span></h1>
                <p class="subtitle">Review and approve incoming event requests ({{ $events->total() }} pending)</p>
            </div>
            <a href="{{ route('admin.events.all') }}" class="btn-secondary">All Events</a>
        </div>

        <!-- Filter -->
        <div class="glass-card fade-in" style="animation-delay:0.05s; padding:16px 24px; margin-bottom:20px;">
            <form action="{{ route('admin.events.pending') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <div class="search-input-wrapper" style="flex:2; min-width:200px;">
                    <span class="search-icon"></span>
                    <input type="text" name="search" class="search-input" style="width:100%;" placeholder="Search events..." value="{{ request('search') }}">
                </div>
                <input type="date" name="date_from" class="form-input" style="flex:1; min-width:150px;" value="{{ request('date_from') }}" placeholder="From date">
                <input type="date" name="date_to" class="form-input" style="flex:1; min-width:150px;" value="{{ request('date_to') }}" placeholder="To date">
                <button type="submit" class="btn-primary">Filter</button>
                @if(request('search') || request('date_from') || request('date_to'))
                    <a href="{{ route('admin.events.pending') }}" class="btn-clear">✖ Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card fade-in" style="animation-delay:0.1s; padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.05); display:flex; align-items:center; gap:10px;">
                <h3 style="margin:0; border:none; padding:0;">Pending Events</h3>
                @if($events->total() > 0)
                    <span class="badge status-pending">{{ $events->total() }}</span>
                @endif
            </div>

            @if($events->count() > 0)
            <div class="table-container" style="border-radius:0; border:none;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Organizer</th>
                            <th>Date & Time</th>
                            <th>Capacity</th>
                            <th>Submitted</th>
                            <th class="actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $event->event_name }}</div>
                                <div style="font-size:0.82rem; color:var(--text-muted);">{{ Str::limit($event->event_description, 60) }}</div>
                            </td>
                            <td>
                                <div class="user-name">
                                    <div class="avatar-placeholder" style="background:linear-gradient(135deg, var(--purple), #a78bfa);">
                                        {{ substr($event->insideUser->fullname ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;">{{ $event->insideUser->fullname ?? 'N/A' }}</div>
                                        <div style="font-size:0.82rem; color:var(--text-muted);">{{ $event->insideUser->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="date-cell">
                                @if($event->event_end_date && !$event->event_date->eq($event->event_end_date)){{ $event->event_date->format('M d') }} – {{ $event->event_end_date->format('M d, Y') }}@else{{ $event->event_date->format('M d, Y') }}@endif<br>
                                <small>{{ $event->event_start_time->format('g:i A') }}</small>
                            </td>
                            <td>Max: <strong>{{ $event->alien_user_limit }}</strong></td>
                            <td class="date-cell">{{ $event->created_at->diffForHumans() }}</td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn-primary btn-sm">Review →</a>
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
                <h3>All Clear!</h3>
                <p>No pending events to review at this time.</p>
            </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>
