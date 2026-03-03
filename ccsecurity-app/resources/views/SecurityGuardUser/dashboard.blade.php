<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Dashboard</title>
</head>
<body>
    <div>
        <h1>Security Guard Dashboard</h1>
        <p>
            Welcome, {{ auth('securityguard')->user()->first_name }} {{ auth('securityguard')->user()->last_name }}
        </p>

        <div>
            <!-- QR Scanner Card -->
            <div>
                <a href="{{ route('security.scanner.show') }}">
                    <h2>QR Scanner</h2>
                </a>
            </div>

            <!-- Entry/Exit Logs Card -->
            <div>
                <a href="{{ route('security.entry.logs') }}">
                    <h2>Entry/Exit Logs</h2>
                </a>
            </div>

            <!-- QR Status Management Card -->
            <div>
                <a href="{{ route('security.qr.status.management') }}">
                    <h2>Inside User QR Status Management</h2>
                </a>
            </div>

            <!-- Shift Management Card -->
            <div>
                <a href="{{ route('security.shift.management') }}">
                    <h2>Shift Management</h2>
                </a>
            </div>

            <!-- Dashboard Info Card -->
            <div>
                <h2>Account Info</h2>
                <p>Email: {{ auth('securityguard')->user()->email }}</p>
                <p>Status: {{ auth('securityguard')->user()->status }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('security.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
