<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - School Security</title>
    @vite(['resources/css/OutsideUSerStyleFolder/outside_user_notifications.css'])
</head>
<body>
    <div class="notifications-container">
        <div class="glass-card">
            
            <div class="page-header">
                <div class="header-title">
                    <h1>Notifications</h1>
                </div>
                <a href="{{ route('outsider.dashboard') }}" class="btn-back">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Dashboard
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ session('success') }}
            </div>
            @endif

            @if($notifications->count() > 0)
            <div class="notifications-wrapper">
                @if($unreadNotificationsCount > 0)
                <form action="{{ route('outsideuser.notifications.read-all') }}" method="POST" style="display:flex;">
                    @csrf
                    <button type="submit" class="btn-mark-all">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                        Mark All as Read ({{ $unreadNotificationsCount }} unread)
                    </button>
                </form>
                @endif

                <div class="notifications-list">
                    @foreach($notifications as $notification)
                    <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
                        <div class="notification-header">
                            <div class="notification-title">
                                @if($notification->type === 'visit_approved')
                                    <span class="type-approved">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px; vertical-align:text-bottom;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        [APPROVED]
                                    </span> {{ $notification->title }}
                                @elseif($notification->type === 'visit_rejected')
                                    <span class="type-rejected">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px; vertical-align:text-bottom;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        [REJECTED]
                                    </span> {{ $notification->title }}
                                @else
                                    <span class="type-general">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px; vertical-align:text-bottom;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    </span> {{ $notification->title }}
                                @endif
                                @if(!$notification->is_read)
                                    <span class="notification-badge">New</span>
                                @endif
                            </div>
                            <div class="notification-time">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="notification-message">{{ $notification->message }}</div>
                        @if(!$notification->is_read)
                        <div class="notification-actions">
                            <form action="{{ route('outsideuser.notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary">Mark as Read</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if($notifications->hasPages())
                <div class="pagination-wrapper">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
            @else
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                <h3>No notifications yet</h3>
                <p>You will be notified when your visit requests are approved or rejected.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
