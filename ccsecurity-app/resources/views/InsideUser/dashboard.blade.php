<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inside User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            color: #007bff;
            text-decoration: none;
            padding: 8px 16px;
            background: #e3f2fd;
            border-radius: 4px;
        }
        .nav-links a:hover {
            background: #bbdefb;
        }
        .notification-badge {
            background: #f44336;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, {{ auth('insideuser')->user()->fullname }}</h1>
            <form method="POST" action="{{ route('insideuser.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background: #f44336; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Logout</button>
            </form>
        </div>

        <div class="nav-links">
            <a href="{{ route('insideuser.profile.show') }}"> Profile</a>
            <a href="{{ route('insideuser.connection.requests') }}">
                 Connection Requests
                @if($pendingConnections->count() > 0)
                    <span class="notification-badge">{{ $pendingConnections->count() }}</span>
                @endif
            </a>
            <a href="{{ route('insideuser.connected.parents') }}"> My Connected Parents</a>
            <a href="{{ route('insideuser.events.dashboard') }}">  My Events</a>
            <a href="#entry-logs"> Entry/Exit Logs</a>
        </div>

        @if($pendingConnections->count() > 0)
        <div class="info-box" style="background: #fff3cd; border-left: 4px solid #ff9800;">
            <h3> Pending Connection Requests</h3>
            <p>You have <strong>{{ $pendingConnections->count() }}</strong> pending connection request(s) waiting for your approval.</p>
            <a href="{{ route('insideuser.connection.requests') }}" style="color: #1976d2; font-weight: bold;">View and respond to requests →</a>
        </div>
        @endif

        @if($connectedParents->count() > 0)
        <div class="info-box" style="background: #d4edda; border-left: 4px solid #4caf50;">
            <h3> Connected Parents/Guardians</h3>
            <p>You have <strong>{{ $connectedParents->count() }}</strong> connected parent(s) who can see your entry/exit records.</p>
            <a href="{{ route('insideuser.connected.parents') }}" style="color: #1976d2; font-weight: bold;">View connected parents →</a>
        </div>
        @endif

        @if($pendingConnections->count() === 0 && $connectedParents->count() === 0)
        <div class="info-box">
            <h3>No Connection Requests</h3>
            <p>You don't have any pending connection requests yet.</p>
            <p>When someone (parent/guardian) requests to connect with you, you'll see it here and can accept or reject the request.</p>
        </div>
        @endif

        {{-- My Events Section --}}
        <div style="margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin: 0;"> My Events</h2>
                <a href="{{ route('insideuser.events.create') }}" style="background: #4caf50; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">+ Create Event</a>
            </div>
            <div class="info-box" style="background: #f3e5f5; border-left: 4px solid #9c27b0; margin-top: 0;">
                <h3>Manage Your Events</h3>
                <p>Create and manage events for alien user registration. Track registrations, approve participants, and generate QR codes.</p>
                <a href="{{ route('insideuser.events.dashboard') }}" style="color: #7b1fa2; font-weight: bold;">View All Events →</a>
            </div>
        </div>

        {{-- Entry/Exit Logs Section --}}
        <div id="entry-logs" style="margin-top: 30px;">
            <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Entry/Exit Logs</h2>
            
            @if($entryLogs->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                    <thead style="background: #1976d2; color: white;">
                        <tr>
                            <th style="padding: 12px; text-align: left;">#</th>
                            <th style="padding: 12px; text-align: left;">Type</th>
                            <th style="padding: 12px; text-align: left;">Scanned By</th>
                            <th style="padding: 12px; text-align: left;">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entryLogs as $index => $log)
                        <tr style="border-bottom: 1px solid #eee; {{ $index % 2 === 1 ? 'background: #f9f9f9;' : '' }}">
                            <td style="padding: 12px;">{{ $index + 1 }}</td>
                            <td style="padding: 12px;">
                                @if($log->scan_type === 'entry')
                                    <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 12px;">ENTRY</span>
                                @elseif($log->scan_type === 'exit')
                                    <span style="background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 12px;">EXIT</span>
                                @else
                                    <span style="background: #e2e3e5; color: #383d41; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 12px;">{{ strtoupper($log->scan_type) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @if($log->securityGuardUser)
                                    {{ $log->securityGuardUser->fullname ?? 'Guard #' . $log->security_guard_user_id }}
                                @else
                                    <em style="color: #999;">System</em>
                                @endif
                            </td>
                            <td style="padding: 12px;">{{ $log->scan_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="margin-top: 15px; color: #666; font-size: 14px;">Showing last {{ $entryLogs->count() }} records. Total: {{ $insideUser->entryLogs()->count() }} logs</p>
            @else
            <div class="info-box" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
                <h3>No Entry/Exit Records</h3>
                <p>You don't have any entry or exit records yet.</p>
                <p>Your entry/exit logs will appear here when you scan your QR code at the security checkpoint.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
