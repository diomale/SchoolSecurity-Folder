<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - School Security</title>
    <style>
        .notification-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
        }
        .notification-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .notification-item.unread {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
        }
        .notification-item.read {
            background-color: #f9f9f9;
            border-left: 4px solid #ccc;
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-title {
            font-weight: bold;
            font-size: 16px;
        }
        .notification-message {
            color: #555;
            margin-bottom: 10px;
        }
        .notification-time {
            font-size: 12px;
            color: #999;
        }
        .btn-mark-read {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-mark-read:hover {
            background-color: #0056b3;
        }
        .btn-mark-all-read {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn-mark-all-read:hover {
            background-color: #218838;
        }
        .back-link {
            margin-bottom: 15px;
            display: inline-block;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div>
        <div>
            <a href="{{ route('outsider.dashboard') }}" class="back-link">← Back to Dashboard</a>
            <h1> Notifications</h1>
        </div>

        @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
        @endif

        @if($notifications->count() > 0)
        <div>
            @if($unreadNotificationsCount > 0)
            <form action="{{ route('outsideuser.notifications.read-all') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-mark-all-read">
                    Mark All as Read ({{ $unreadNotificationsCount }} unread)
                </button>
            </form>
            @endif

            @foreach($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
                <div class="notification-header">
                    <div class="notification-title">
                        @if($notification->type === 'visit_approved')
                            [APPROVED] {{ $notification->title }}
                        @elseif($notification->type === 'visit_rejected')
                            [REJECTED] {{ $notification->title }}
                        @else
                            {{ $notification->title }}
                        @endif
                        @if(!$notification->is_read)
                            <span class="notification-badge">New</span>
                        @endif
                    </div>
                    <div class="notification-time">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="notification-message">{{ $notification->message }}</div>
                @if(!$notification->is_read)
                <form action="{{ route('outsideuser.notifications.read', $notification->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-mark-read">Mark as Read</button>
                </form>
                @endif
            </div>
            @endforeach

            @if($notifications->hasPages())
            <div style="margin-top: 20px;">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
        @else
        <div class="empty-state">
            <p> No notifications yet</p>
            <p>You will be notified when your visit requests are approved or rejected.</p>
        </div>
        @endif
    </div>
</body>
</html>
