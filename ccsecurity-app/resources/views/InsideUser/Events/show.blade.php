<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->event_name }} - Event Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d1ecf1; color: #0c5460; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .qr-container { border: 2px dashed #ddd; padding: 15px; border-radius: 8px; text-align: center; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <a href="{{ route('insideuser.events.dashboard') }}" class="nav-link" style="font-size: 12px;">← Back to Events</a>
                    <h1 style="margin: 5px 0 0 0; font-size: 24px;">{{ $event->event_name }}</h1>
                </div>
                <div>
                    @if($event->status === 'approved')
                        <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-success">Manage Registrations</a>
                        <a href="{{ route('insideuser.events.edit', $event->id) }}" class="btn btn-primary">Edit Event</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Event Information</h2>
                    <div class="info-row"><span style="color: #666;">Event Name</span><strong>{{ $event->event_name }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Description</span><span>{{ $event->event_description ?? 'No description' }}</span></div>
                    <div class="info-row"><span style="color: #666;">Date</span><strong>{{ $event->event_date->format('l, F d, Y') }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Time</span><strong>{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Registration Deadline</span><strong>{{ $event->qr_request_deadline->format('M d, Y g:i A') }}</strong></div>
                </div>

                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Event Status</h2>
                    @if($event->status === 'pending')
                        <span class="badge badge-yellow"> Pending Approval</span>
                    @elseif($event->status === 'approved')
                        <span class="badge badge-green"> Approved</span>
                    @elseif($event->status === 'completed')
                        <span class="badge badge-blue"> Completed</span>
                    @elseif($event->status === 'cancelled')
                        <span class="badge badge-red"> Cancelled</span>
                    @endif
                    @if($event->admin_remarks)
                        <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                            <strong>Admin Remarks:</strong><br>{{ $event->admin_remarks }}
                        </div>
                    @endif
                </div>

                @if($event->status === 'approved' && $registrations->count() > 0)
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2 style="margin: 0; font-size: 18px;">Recent Registrations</h2>
                        <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="nav-link">View All →</a>
                    </div>
                    <table>
                        <thead>
                            <tr><th>Name</th><th>Email</th><th>QR Code</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($registrations->take(5) as $reg)
                            <tr>
                                <td>{{ $reg->fullname }}</td>
                                <td>{{ $reg->email }}</td>
                                <td><code style="font-size: 11px;">{{ $reg->qr_code }}</code></td>
                                <td><span class="badge badge-green">{{ ucfirst($reg->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <div>
                @if($event->status === 'approved')
                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Share Event</h2>
                    <div class="qr-container">
                        {!! QrCode::size(150)->generate(route('public.event.register', ['code' => $event->id])) !!}
                    </div>
                    <p style="font-size: 12px; color: #666; text-align: center;">Scan to register for this event</p>
                    <p style="font-size: 10px; color: #999; text-align: center; word-break: break-all; padding: 0 10px;">
                        {{ route('public.event.register', ['code' => $event->id]) }}
                    </p>
                </div>

                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Public Visibility</h2>
                    <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                        Show this event on the welcome page for public registration
                    </p>
                    <form action="{{ route('insideuser.events.toggle-welcome', $event->id) }}" method="POST">
                        @csrf
                        @if($event->show_on_welcome)
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Hide from Welcome Page
                            </button>
                            <p style="font-size: 12px; color: #28a745; margin: 10px 0 0 0; text-align: center;">
                                ✓ Currently visible on welcome page
                            </p>
                        @else
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                Show on Welcome Page
                            </button>
                            <p style="font-size: 12px; color: #666; margin: 10px 0 0 0; text-align: center;">
                                Currently hidden from welcome page
                            </p>
                        @endif
                    </form>
                </div>

                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Registration Approvals</h2>
                    @php
                        $pendingCount = $event->pendingApprovals()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <a href="{{ route('insideuser.events.approvals.pending', $event->id) }}" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                            View Pending Approvals ({{ $pendingCount }})
                        </a>
                    @endif
                    <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-success" style="width: 100%;">
                        Manage All Registrations
                    </a>
                </div>
                @endif

                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Quick Stats</h2>
                    <div class="stats-grid" style="grid-template-columns: 1fr;">
                        <div class="stat-card" style="background: #e3f2fd;">
                            <div style="font-size: 24px; font-weight: bold; color: #1976d2;">{{ $event->registrations_count }}</div>
                            <div style="font-size: 12px; color: #666;">Registered</div>
                        </div>
                        <div class="stat-card" style="background: #e8f5e9;">
                            <div style="font-size: 24px; font-weight: bold; color: #388e3c;">{{ $event->alien_user_limit - $event->registrations_count }}</div>
                            <div style="font-size: 12px; color: #666;">Available Slots</div>
                        </div>
                        <div class="stat-card" style="background: #f3e5f5;">
                            <div style="font-size: 24px; font-weight: bold; color: #7b1fa2;">{{ $event->alien_user_limit }}</div>
                            <div style="font-size: 12px; color: #666;">Total Limit</div>
                        </div>
                    </div>
                </div>

                @if($event->status !== 'completed' && $event->status !== 'cancelled')
                <div class="card">
                    <h2 style="margin: 0 0 15px 0; font-size: 18px;">Actions</h2>
                    @if($event->status === 'pending')
                        <button disabled class="btn btn-danger" style="width: 100%;">Cancel Event</button>
                    @else
                        <form action="{{ route('insideuser.events.cancel', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="width: 100%;">Cancel Event</button>
                        </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
