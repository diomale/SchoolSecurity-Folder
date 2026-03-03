<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard - School Security</title>
    <style>
        .notification-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .notification-item.unread {
            background-color: #e7f3ff;
            border-left: 3px solid #007bff;
        }
        .notification-item.read {
            background-color: #f9f9f9;
            border-left: 3px solid #ccc;
        }
        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .notification-message {
            color: #666;
            font-size: 14px;
        }
        .notification-time {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .btn-mark-read {
            padding: 5px 10px;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div>
        <div>
            <h1>Welcome, {{ auth('outsideuser')->user()->fullname }}</h1>
            <a href="{{ route('outsideuser.profile.show') }}"> My Profile</a>
            <a href="{{ route('outsideuser.notifications') }}">
                Notifications
                @if($unreadNotificationsCount > 0)
                    <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                @endif
            </a>
        </div>

        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div>
            {{ session('error') }}
        </div>
        @endif

        @if($notifications->count() > 0)
        <div>
            <h2>Recent Notifications</h2>
            @foreach($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
                <div class="notification-title">
                    @if($notification->type === 'visit_approved')
                        ✓ {{ $notification->title }}
                    @elseif($notification->type === 'visit_rejected')
                        ✗ {{ $notification->title }}
                    @else
                        {{ $notification->title }}
                    @endif
                    @if(!$notification->is_read)
                        <span class="notification-badge">New</span>
                    @endif
                </div>
                <div class="notification-message">{{ $notification->message }}</div>
                <div class="notification-time">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                @if(!$notification->is_read)
                <form action="{{ route('outsideuser.notifications.read', $notification->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-mark-read">Mark as Read</button>
                </form>
                @endif
            </div>
            @endforeach
            <p><a href="{{ route('outsideuser.notifications') }}">View All Notifications</a></p>
        </div>
        @endif

        <div>
            <h2>Quick Actions</h2>

            @if(auth('outsideuser')->user()->status == 1)
                <div>
                    <h3>Request a Visit</h3>
                    <p>Submit a visit request to activate your QR code</p>
                    <a href="{{ route('outsideuser.visit.request') }}">Request Visit</a>
                </div>

                <div>
                    <h3>Visit History</h3>
                    <p>View your past and upcoming visit requests</p>
                    <a href="{{ route('outsideuser.visit.history') }}">View Visit History</a>
                </div>

            @else
                <div>
                    <p>Your account is pending admin approval. Please wait for approval before requesting visits.</p>
                </div>
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('outsideuser.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
