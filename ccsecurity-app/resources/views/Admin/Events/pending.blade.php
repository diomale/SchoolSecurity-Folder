<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Event Approvals</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .nav-link { color: #007bff; text-decoration: none; }
        .filter-form { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; margin-bottom: 20px; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;">Pending Event Approvals</h1>
                    <p style="margin: 5px 0 0 0; color: #666;">Review and approve event requests</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Dashboard</a>
                    <a href="{{ route('admin.events.all') }}" class="btn btn-primary">All Events</a>
                </div>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('admin.events.pending') }}" method="GET" class="filter-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events...">
                <input type="date" name="date_from" value="{{ request('date_from') }}">
                <input type="date" name="date_to" value="{{ request('date_to') }}">
                <button type="submit" class="btn btn-primary"> Filter</button>
            </form>
        </div>

        <div class="card">
            <h2 style="margin: 0 0 15px 0;">Events Pending ({{ $events->total() }})</h2>
            @if($events->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Organizer</th>
                        <th>Date & Time</th>
                        <th>Limit</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $event->event_name }}</div>
                            <div style="font-size: 12px; color: #666;">{{ Str::limit($event->event_description, 60) }}</div>
                        </td>
                        <td>
                            <div>{{ $event->insideUser->fullname ?? 'N/A' }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $event->insideUser->email ?? '' }}</div>
                        </td>
                        <td>
                            <div>{{ $event->event_date->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $event->event_start_time->format('g:i A') }}</div>
                        </td>
                        <td>Max: {{ $event->alien_user_limit }}</td>
                        <td>{{ $event->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.events.show', $event->id) }}" class="nav-link">Review →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $events->links() }}</div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <p style="color: #666;">No pending events to review.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
