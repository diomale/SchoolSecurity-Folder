<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>Admin Dashboard</h1>
            <div>
                <span>Welcome, {{ auth('admin')->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div>
            <h2>Management Sections</h2>
            
            <div>
                <!-- QR Status Management Card -->
                <div>
                    <a href="{{ route('admin.qr.status.management') }}">
                        <h3>QR Status Management</h3>
                        
                    </a>
                </div>

                <!-- Inside User CRUD Card -->
                <div>
                    <a href="{{ route('admin.show.crudSection') }}">
                        <h3>Inside User Management</h3>
                        
                    </a>
                </div>

                <!-- Security Guard Management Card -->
                <div>
                    <a href="{{ route('security.user.table.section') }}">
                        <h3>Security Guard Management</h3>
                       
                    </a>
                </div>

                <!-- Waiting List Card -->
                <div>
                    <a href="{{ route('show.admin.outsider.list') }}">
                        <h3>Outsider Management</h3>
                        
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div>
            <p>&copy; {{ date('Y') }} School Security System</p>
        </div>
    </div>
</body>
</html>
