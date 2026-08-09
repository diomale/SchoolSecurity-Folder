<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Analytics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .nav-link { color: #007bff; text-decoration: none; }
        .creator-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .creator-rank { width: 36px; height: 36px; border-radius: 50%; background: #e3f2fd; color: #1976d2; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 12px; }
        .creator-info { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;">Event Analytics</h1>
                    <p style="margin: 5px 0 0 0; color: #666;">System-wide event statistics</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
                    <a href="{{ route('admin.events.all') }}" class="btn btn-primary">All Events</a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Total Events</div><div style="font-size: 24px; font-weight: bold;">{{ $statistics['total_events'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Total Registrations</div><div style="font-size: 24px; font-weight: bold; color: #6f42c1;">{{ $statistics['total_registrations'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Pending</div><div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $statistics['pending_events'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Approved</div><div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $statistics['approved_events'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Today's Events</div><div style="font-size: 24px; font-weight: bold; color: #007bff;">{{ $statistics['today_events'] }}</div></div>
            <div class="stat-card"><div style="font-size: 12px; color: #666;">Checked In Today</div><div style="font-size: 24px; font-weight: bold; color: #6610f2;">{{ $statistics['checked_in_today'] }}</div></div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h2 style="margin: 0 0 15px 0;">Top Event Creators</h2>
                @if($topCreators->count() > 0)
                    @foreach($topCreators as $index => $creator)
                    <div class="creator-item">
                        <div style="display: flex; align-items: center;">
                            <div class="creator-rank">{{ $index + 1 }}</div>
                            <div class="creator-info">
                                <div style="font-weight: 600;">{{ $creator->insideUser->fullname ?? 'N/A' }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $creator->insideUser->email ?? '' }}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 20px; font-weight: bold;">{{ $creator->event_count }}</div>
                            <div style="font-size: 12px; color: #666;">events</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #666; padding: 40px 0;">No event creators yet.</p>
                @endif
            </div>

            <div class="card">
                <h2 style="margin: 0 0 15px 0;">Recent Events</h2>
                @if($recentEvents->count() > 0)
                    @foreach($recentEvents as $event)
                    <div style="padding: 12px 0; border-bottom: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-weight: 600;">{{ $event->event_name }}</div>
                                <div style="font-size: 12px; color: #666;">by {{ $event->insideUser->fullname ?? 'N/A' }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 14px; font-weight: 600;">{{ $event->registrations_count }} regs</div>
                                <div style="font-size: 12px; color: #999;">{{ $event->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #666; padding: 40px 0;">No recent events.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
