<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d1ecf1; color: #0c5460; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .nav-link {
            color: #007bff;
            text-decoration: none;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1 style="margin: 0; font-size: 24px;">My Events</h1>
                <p style="margin: 5px 0 0 0; color: #666;">Manage and track your events</p>
            </div>
            <div>
                <a href="{{ route('insideuser.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
                <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Events</h3>
                <div class="value" style="color: #007bff;">{{ $totalEvents }}</div>
            </div>
            <div class="stat-card">
                <h3>Pending Approval</h3>
                <div class="value" style="color: #ffc107;">{{ $pendingEvents }}</div>
            </div>
            <div class="stat-card">
                <h3>Approved Events</h3>
                <div class="value" style="color: #28a745;">{{ $approvedEvents }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Registrations</h3>
                <div class="value" style="color: #6f42c1;">{{ $totalRegistrations }}</div>
            </div>
        </div>

        <!-- Events Table -->
        <div class="table-container">
            <div class="table-header">
                <h2 style="margin: 0; font-size: 18px;">Your Events</h2>
            </div>

            @if($events->count() > 0)
            <table>
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
                            <div style="font-weight: 600;">{{ $event->event_name }}</div>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">{{ Str::limit($event->event_description, 50) }}</div>
                        </td>
                        <td>
                            <div>{{ $event->event_date->format('M d, Y') }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</div>
                        </td>
                        <td>
                            <div>{{ $event->registrations_count }} / {{ $event->alien_user_limit }}</div>
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
                            <a href="{{ route('insideuser.events.show', $event->id) }}" class="nav-link">View</a>
                            @if($event->status === 'approved')
                                <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="nav-link">Registrations</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($events->hasPages())
            <div style="padding: 20px;">
                {{ $events->links() }}
            </div>
            @endif
            @else
            <div class="empty-state">
                <svg style="width: 48px; height: 48px; color: #ccc; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 style="margin: 20px 0 10px 0;">No events yet</h3>
                <p style="color: #666; margin-bottom: 20px;">Get started by creating your first event.</p>
                <a href="{{ route('insideuser.events.create') }}" class="btn btn-primary">+ Create Event</a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
