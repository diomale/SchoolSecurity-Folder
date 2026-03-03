<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Dashboard</title>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button.active { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>Security Guard Dashboard</h1>
            <p>Welcome, <strong>{{ $guard->fullname }}</strong></p>
            <div>
                <a href="{{ route('security.scanner.show') }}">QR Scanner</a> |
                <a href="{{ route('security.entry.logs') }}">Entry/Exit Logs</a> |
                <a href="{{ route('security.shift.management') }}">Shift Management</a> |
                <a href="{{ route('security.qr.status.management') }}">QR Status Management</a> |
                <form method="POST" action="{{ route('security.logout') }}" style="display:inline;">
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

        <hr>

        <!-- Tab Navigation -->
        <div>
            <button class="tab-button active" onclick="switchTab('dashboard')">Dashboard</button>
            <button class="tab-button" onclick="switchTab('profile')">User Profile</button>
            <button class="tab-button" onclick="switchTab('notifications')">Notifications</button>
        </div>

        <hr>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <h2>Dashboard Overview</h2>

            <!-- Statistics -->
            <div>
                <h3>Today's Statistics</h3>
                <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                    <tr>
                        <th>Total Scans Today</th>
                        <th>Entries Today</th>
                        <th>Exits Today</th>
                        <th>Total Scans (All Time)</th>
                    </tr>
                    <tr>
                        <td>{{ $todayScans }}</td>
                        <td>{{ $todayEntries }}</td>
                        <td>{{ $todayExits }}</td>
                        <td>{{ $totalScans }}</td>
                    </tr>
                </table>
            </div>

            <hr>

            <!-- Quick Actions -->
            <div>
                <h3>Quick Actions</h3>
                <ul>
                    <li>
                        <a href="{{ route('security.scanner.show') }}">
                            <strong> QR Scanner</strong> - Scan QR codes to log entry and exit of users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('security.entry.logs') }}">
                            <strong> Entry/Exit Logs</strong> - View all people entering and exiting the premises
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('security.shift.management') }}">
                            <strong> Shift Management</strong> - Clock in/out and view your shift schedule
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('security.qr.status.management') }}">
                            <strong> QR Status Management</strong> - Activate or deactivate user QR codes
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- User Profile Tab -->
        <div id="profile" class="tab-content">
            <h2>User Profile</h2>

            <div>
                <h3>Guard Information</h3>
                <table border="1" cellpadding="10">
                    <tr>
                        <th>Full Name:</th>
                        <td>{{ $guard->fullname }}</td>
                    </tr>
                    <tr>
                        <th>First Name:</th>
                        <td>{{ $guard->first_name }}</td>
                    </tr>
                    <tr>
                        <th>Last Name:</th>
                        <td>{{ $guard->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $guard->email }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($guard->status == 1)
                                ✓ Active
                            @else
                                ✗ Inactive
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <hr>

            <div>
                <h3>My Statistics</h3>
                <table border="1" cellpadding="10">
                    <tr>
                        <th>Total Scans</th>
                        <th>Scans Today</th>
                        <th>Entries Today</th>
                        <th>Exits Today</th>
                    </tr>
                    <tr>
                        <td>{{ $totalScans }}</td>
                        <td>{{ $todayScans }}</td>
                        <td>{{ $todayEntries }}</td>
                        <td>{{ $todayExits }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="notifications" class="tab-content">
            <h2>Notifications - QR Status Management Activities</h2>
            <p><em>View recent QR code activations/deactivations and scan activities by all security guards.</em></p>

            @if($recentActivities->count() > 0)
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>User Type</th>
                        <th>Activity Type</th>
                        <th>Details</th>
                        <th>Guard</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities as $activity)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($activity->scan_at)->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($activity->insideUser)
                                {{ $activity->insideUser->fullname }}
                            @elseif($activity->outsideUser)
                                {{ $activity->outsideUser->fullname }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($activity->insideUser)
                                Inside User
                            @elseif($activity->outsideUser)
                                Outside User (Visitor)
                            @else
                                QR Status Change
                            @endif
                        </td>
                        <td>
                            @if(str_starts_with($activity->scan_type, 'qr_'))
                                 QR Status Toggle
                            @else
                                 {{ strtoupper($activity->scan_type) }}
                            @endif
                        </td>
                        <td>
                            @if(str_starts_with($activity->scan_type, 'qr_'))
                                @php
                                    $status = str_replace('qr_', '', $activity->scan_type);
                                @endphp
                                QR code <strong>{{ strtoupper($status) }}</strong>
                            @elseif($activity->scan_type === 'entry')
                                User entered the premises
                            @elseif($activity->scan_type === 'exit')
                                User exited the premises
                            @else
                                {{ $activity->scan_type }}
                            @endif
                        </td>
                        <td>
                            @php
                                $guardName = 'Unknown';
                                if ($activity->securityGuardUser) {
                                    $guardName = $activity->securityGuardUser->fullname;
                                    if (empty($guardName)) {
                                        $guardName = trim($activity->securityGuardUser->first_name . ' ' . $activity->securityGuardUser->last_name);
                                    }
                                    if (empty($guardName)) {
                                        $guardName = 'Guard ID: ' . $activity->security_guard_user_id;
                                    }
                                } else {
                                    $guardName = 'Guard ID: ' . $activity->security_guard_user_id;
                                }
                            @endphp
                            {{ $guardName }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>No recent activities found.</p>
            @endif
        </div>
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
    </script>
</body>
</html>
