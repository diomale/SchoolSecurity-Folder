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
            <a href="{{ route('admin.profile.show') }}">{{ auth('admin')->user()->name }}</a>
        </div>
        <hr>

        <!-- Main Content -->
        <div>
            <h2>Management Sections</h2>
            
            <div>
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
