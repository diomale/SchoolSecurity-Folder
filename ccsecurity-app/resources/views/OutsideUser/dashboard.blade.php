<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard - School Security</title>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button.active { font-weight: bold; text-decoration: underline; }
        .notification-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
        
        /* QR Code Protection Styles */
        .qr-code-container {
            display: inline-block;
            position: relative;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        
        .qr-code-container img {
            display: block;
            pointer-events: none;
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
        
        .qr-code-container::after {
            content: 'Right-click disabled';
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #999;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .qr-code-container:hover::after {
            opacity: 1;
        }
        
        /* Status Styles */
        .status-active {
            color: #4caf50;
            font-weight: 600;
        }
        
        .status-inactive {
            color: #f44336;
            font-weight: 600;
        }
        
        .qr-instruction {
            color: #666;
            font-style: italic;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>Welcome, {{ auth('outsideuser')->user()->fullname }}</h1>
            <div>
                <a href="{{ route('outsideuser.notifications') }}">
                    Notifications
                    @if($unreadNotificationsCount > 0)
                        <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a> |
                <form method="POST" action="{{ route('outsideuser.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div>
            <strong>Success:</strong> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div>
            <strong>Error:</strong> {{ session('error') }}
        </div>
        @endif

        <!-- Tab Navigation -->
        <div>
            <button class="tab-button active" onclick="switchTab('profile')">User Profile</button>
            <button class="tab-button" onclick="switchTab('quick-actions')">Visit Request</button>
            <button class="tab-button" onclick="switchTab('visit-history')">Visit History</button>
            <button class="tab-button" onclick="switchTab('child-connections')">
                Child Connections
                @if($pendingConnectionCount > 0)
                    <span class="notification-badge">{{ $pendingConnectionCount }}</span>
                @endif
            </button>
        </div>

        <hr>

        <!-- User Profile Tab -->


        <div id="profile" class="tab-content active">
            <div>
                <h2>User Profile</h2>
                <h3>My QR Code Pass</h3>
                @if(auth('outsideuser')->user()->qr_value)
                    <div class="qr-code-container">
                        {!! QrCode::size(200)->margin(1)->generate(auth('outsideuser')->user()->qr_value) !!}
                    </div>
                    <p>
                        <strong>QR Status:</strong>
                        @if(auth('outsideuser')->user()->qr_status === 'active')
                            <span class="status-active">● ACTIVE</span>
                        @else
                            <span class="status-inactive">● INACTIVE</span>
                        @endif
                    </p>

                    <p class="qr-instruction">
                        <em>Present this QR code to the guard at the entrance.</em>
                    </p>
                @else
                    <p>No QR code generated yet.</p>
                @endif
            </div>

            
            <div>
                <h3>Profile Information</h3>
                <p><strong>Name:</strong> {{ auth('outsideuser')->user()->fullname }}</p>
                <p><strong>Email:</strong> {{ auth('outsideuser')->user()->email }}</p>
                <p><strong>Phone:</strong> {{ auth('outsideuser')->user()->phone_number }}</p>
                <a href="{{ route('outsideuser.profile.show') }}">Edit Profile</a>
            </div>

                <hr>
            

            <div>
                <h3>Statistics</h3>
                <ul>
                    <li><strong>Total Requests:</strong> {{ $visitRequests->count() }}</li>
                    <li><strong>Approved:</strong> {{ $visitRequests->where('status', 'approved')->count() }}</li>
                    <li><strong>Pending:</strong> {{ $visitRequests->where('status', 'pending')->count() }}</li>
                    <li><strong>Rejected:</strong> {{ $visitRequests->where('status', 'rejected')->count() }}</li>
                </ul>
            </div>
        </div>

        <!-- Quick Actions Tab -->
        <div id="quick-actions" class="tab-content">
            <h2>Visit Request</h2>

            <div>
                <h3>Request a Visit</h3>
                <p>Submit a visit request to activate your QR code</p>
                <a href="{{ route('outsideuser.visit.request') }}">Request Visit</a>
            </div>

        </div>

        <!-- Visit History Tab -->
        <div id="visit-history" class="tab-content">
            <h2>Visit History</h2>

            @if($visitRequests->count() > 0)
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Visit Date</th>
                        <th>Time</th>
                        <th>Purpose</th>
                        <th>Person to Meet</th>
                        <th>Status</th>
                        <th>Admin Remarks</th>
                        <th>Requested On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visitRequests as $request)
                    <tr>
                        <td>{{ $request->visit_date->format('M d, Y') }}</td>
                        <td>{{ $request->visit_time->format('h:i A') }}</td>
                        <td>{{ $request->purpose }}</td>
                        <td>{{ $request->person_to_meet }}</td>
                        <td>
                            @if($request->status === 'approved')
                                 Approved
                            @elseif($request->status === 'rejected')
                                 Rejected
                            @else
                                 Pending
                            @endif
                        </td>
                        <td>
                            @if($request->admin_remarks)
                                {{ $request->admin_remarks }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>No visit requests found.</p>
            <a href="{{ route('outsideuser.visit.request') }}">Request a Visit</a>
            @endif
        </div>

        <!-- Child Connections Tab -->
        <div id="child-connections" class="tab-content">
            <h2> Child Connections</h2>
            <p>Connect with your children to track their entry and exit at school</p>
            
            <div style="margin-bottom: 20px;">
                <a href="{{ route('outsideuser.connections.request') }}" style="background: #4caf50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">+ Request New Connection</a>
                <a href="{{ route('outsideuser.connections.history') }}" style="background: #2196f3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-left: 10px;">View History</a>
            </div>

            @if($approvedConnections->count() > 0)
            <h3>Connected Children</h3>
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>QR Value</th>
                        <th>Relationship</th>
                        <th>Connected Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedConnections as $connection)
                    <tr>
                        <td>{{ $connection->insideUser->fullname ?? 'N/A' }}</td>
                        <td>{{ $connection->insideUser->email ?? 'N/A' }}</td>
                        <td>
                            @if($connection->insideUser->qr_status === 'active')
                                <span style="color: #4caf50; font-weight: 600;">● ACTIVE</span>
                            @else
                                <span style="color: #f44336; font-weight: 600;">● INACTIVE</span>
                            @endif
                        </td>
                        <td>{{ $connection->relationship }}</td>
                        <td>{{ \Carbon\Carbon::parse($connection->approved_at)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <p style="color: #666;">You haven't connected with any children yet.</p>
                <a href="{{ route('outsideuser.connections.request') }}">Request your first connection</a>
            </div>
            @endif

            @if($pendingConnectionCount > 0)
            <h3>Pending Requests</h3>
            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;">
                <p>You have <strong>{{ $pendingConnectionCount }}</strong> pending connection request(s) awaiting admin approval.</p>
                <a href="{{ route('outsideuser.connections.history') }}">View connection history</a>
            </div>
            @endif

            <!-- Children Entry/Exit Logs -->
            @if(isset($childrenEntryLogs) && count($childrenEntryLogs) > 0)
            <hr style="margin: 30px 0;">
            <h3> Recent Entry/Exit Activity</h3>
            <p style="color: #666; margin-bottom: 15px;">Track when your children enter or exit the school</p>
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Scan Type</th>
                        <th>Scanned By</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($childrenEntryLogs as $log)
                    <tr>
                        <td>
                            <strong>{{ $log->insideUser->fullname ?? 'Unknown' }}</strong>
                        </td>
                        <td>
                            @if($log->scan_type === 'entry')
                                <span style="color: #4caf50; font-weight: bold;">ENTRY</span>
                            @elseif($log->scan_type === 'exit')
                                <span style="color: #f44336; font-weight: bold;">EXIT</span>
                            @else
                                {{ $log->scan_type ?? 'N/A' }}
                            @endif
                        </td>
                        <td>
                            {{ $log->securityGuardUser->fullname ?? 'Unknown Guard' }}
                        </td>
                        <td>
                            @if($log->scan_at)
                                {{ $log->scan_at->format('M d, Y h:i A') }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <!-- Notifications Section -->
        @if($notifications->count() > 0)
        <hr>
        <div>
            <h2>Recent Notifications</h2>
            @foreach($notifications as $notification)
            <div>
                <strong>
                    @if($notification->type === 'visit_approved')
                        
                    @elseif($notification->type === 'visit_rejected')
                        
                    @endif
                    {{ $notification->title }}
                    @if(!$notification->is_read)
                        <span class="notification-badge">New</span>
                    @endif
                </strong>
                <p>{{ $notification->message }}</p>
                <small>{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                @if(!$notification->is_read)
                <form action="{{ route('outsideuser.notifications.read', $notification->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Mark as Read</button>
                </form>
                @endif
                <hr>
            </div>
            @endforeach
            <p><a href="{{ route('outsideuser.notifications') }}">View All Notifications</a></p>
        </div>
        @endif
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            var contents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < contents.length; i++) {
                contents[i].classList.remove('active');
            }

            // Remove active class from all buttons
            var buttons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }
        
        // Disable right-click on QR code
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.querySelector('.qr-code-container');
            if (qrContainer) {
                qrContainer.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                
                // Disable drag start on QR image
                const qrImage = qrContainer.querySelector('img');
                if (qrImage) {
                    qrImage.addEventListener('dragstart', function(e) {
                        e.preventDefault();
                        return false;
                    });
                }
            }
        });
    </script>
</body>
</html>
