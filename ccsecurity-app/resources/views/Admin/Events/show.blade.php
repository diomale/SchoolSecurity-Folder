<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Review - {{ $event->event_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; width: 100%; margin-bottom: 10px; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .badge { padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; resize: vertical; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <a href="{{ route('admin.events.pending') }}" class="nav-link" style="font-size: 12px;">← Back to Pending</a>
                    <h1 style="margin: 5px 0 0 0; font-size: 24px;">{{ $event->event_name }}</h1>
                </div>
                <a href="{{ route('admin.events.all') }}" class="btn btn-secondary" style="width: auto;">All Events</a>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Event Information</h2>
                    <div class="info-row"><span style="color: #666;">Event Name</span><strong>{{ $event->event_name }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Description</span><span>{{ $event->event_description ?? 'N/A' }}</span></div>
                    <div class="info-row"><span style="color: #666;">Date</span><strong>{{ $event->event_date->format('l, F d, Y') }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Time</span><strong>{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Registration Deadline</span><strong>{{ $event->qr_request_deadline->format('M d, Y g:i A') }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Participant Limit</span><strong>{{ $event->alien_user_limit }}</strong></div>
                </div>

                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Organizer Information</h2>
                    <div class="info-row"><span style="color: #666;">Name</span><strong>{{ $event->insideUser->fullname ?? 'N/A' }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Email</span><strong>{{ $event->insideUser->email ?? 'N/A' }}</strong></div>
                    <div class="info-row"><span style="color: #666;">User ID</span><strong>{{ $event->inside_user_id }}</strong></div>
                </div>

                @if($recentRegistrations->count() > 0)
                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Recent Registrations ({{ $recentRegistrations->count() }})</h2>
                    <table>
                        <thead><tr><th>Name</th><th>Email</th><th>QR Code</th></tr></thead>
                        <tbody>
                            @foreach($recentRegistrations as $reg)
                            <tr>
                                <td>{{ $reg->fullname }}</td>
                                <td>{{ $reg->email }}</td>
                                <td><code style="font-size: 11px;">{{ $reg->qr_code }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <div>
                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Current Status</h2>
                    @if($event->status === 'pending')
                        <span class="badge badge-yellow"> Pending Approval</span>
                    @elseif($event->status === 'approved')
                        <span class="badge badge-green"> Approved</span>
                    @elseif($event->status === 'rejected')
                        <span class="badge badge-red"> Rejected</span>
                    @endif
                    @if($event->admin_remarks)
                        <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                            <strong>Admin Remarks:</strong><br>{{ $event->admin_remarks }}
                        </div>
                    @endif
                </div>

                @if($event->status === 'pending')
                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Approval Actions</h2>
                    
                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST">
                        @csrf
                        <label style="font-weight: 600; margin-bottom: 8px; display: block;">Admin Remarks (Optional)</label>
                        <textarea name="admin_remarks" rows="3" placeholder="Add any notes or conditions..."></textarea>
                        <button type="submit" class="btn btn-success" style="margin-top: 10px;" onclick="return confirm('Approve this event?')"> Approve Event</button>
                    </form>

                    <form action="{{ route('admin.events.reject', $event->id) }}" method="POST" style="margin-top: 15px;">
                        @csrf
                        <label style="font-weight: 600; margin-bottom: 8px; display: block;">Rejection Reason <span style="color: #dc3545;">*</span></label>
                        <textarea name="admin_remarks" rows="3" required placeholder="Explain why this event is rejected..."></textarea>
                        <button type="submit" class="btn btn-danger" style="margin-top: 10px;" onclick="return confirm('Reject this event?')"> Reject Event</button>
                    </form>
                </div>
                @endif

                <div class="card">
                    <h2 style="margin: 0 0 15px 0;">Quick Stats</h2>
                    <div class="info-row"><span style="color: #666;">Days Until Event</span><strong>{{ max(0, now()->diffInDays($event->event_date)) }}</strong></div>
                    <div class="info-row"><span style="color: #666;">Created</span><strong>{{ $event->created_at->diffForHumans() }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
