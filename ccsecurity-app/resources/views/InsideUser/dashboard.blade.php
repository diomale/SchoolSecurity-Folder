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
            <h3>✓ Connected Parents/Guardians</h3>
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
    </div>
</body>
</html>
