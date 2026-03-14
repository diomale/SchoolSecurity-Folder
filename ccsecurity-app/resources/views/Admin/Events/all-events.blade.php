<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-gray { background: #e2e3e5; color: #383d41; }
        .badge-blue { background: #d1ecf1; color: #0c5460; }
        .nav-link { color: #007bff; text-decoration: none; }
        .filter-form { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; margin-bottom: 20px; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;">All Events Management</h1>
                    <p style="margin: 5px 0 0 0; color: #666;">View and manage all system events</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Dashboard</a>
                    <a href="{{ route('admin.events.pending') }}" class="btn btn-primary">Pending Approvals</a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Total</div><div style="font-size: 24px; font-weight: bold;">{{ $statistics['total'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Pending</div><div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $statistics['pending'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Approved</div><div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $statistics['approved'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Rejected</div><div style="font-size: 24px; font-weight: bold; color: #dc3545;">{{ $statistics['rejected'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Completed</div><div style="font-size: 24px; font-weight: bold; color: #007bff;">{{ $statistics['completed'] }}</div></div>
        </div>

        <div class="card">
            <form action="{{ route('admin.events.all') }}" method="GET" class="filter-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events or organizers...">
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
            </form>
        </div>

        <div class="card">
            <h2 style="margin: 0 0 15px 0;">Events List</h2>
            @if($events->count() > 0)
            <table>
                <thead>
                    <tr><th>Event</th><th>Organizer</th><th>Date</th><th>Registrations</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $event->event_name }}</div>
                            <div style="font-size: 12px; color: #666;">{{ Str::limit($event->event_description, 50) }}</div>
                        </td>
                        <td>
                            <div>{{ $event->insideUser->fullname ?? 'N/A' }}</div>
                            <div style="font-size: 12px; color: #666;">ID: {{ $event->inside_user_id }}</div>
                        </td>
                        <td>
                            <div>{{ $event->event_date->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $event->event_start_time->format('g:i A') }}</div>
                        </td>
                        <td>{{ $event->registrations_count }} / {{ $event->alien_user_limit }}</td>
                        <td>
                            @if($event->status === 'pending')<span class="badge badge-yellow">Pending</span>
                            @elseif($event->status === 'approved')<span class="badge badge-green">Approved</span>
                            @elseif($event->status === 'rejected')<span class="badge badge-red">Rejected</span>
                            @elseif($event->status === 'cancelled')<span class="badge badge-gray">Cancelled</span>
                            @elseif($event->status === 'completed')<span class="badge badge-blue">Completed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.events.show', $event->id) }}" class="nav-link">View</a>
                            @if($event->status === 'approved')
                                <form action="{{ route('admin.events.mark-completed', $event->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;" onclick="return confirm('Mark as completed?')">✓ Complete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $events->links() }}</div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <p style="color: #666;">No events found.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
