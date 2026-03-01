<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Guard Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Security Guard Dashboard</h1>
        <p class="text-lg text-gray-600 mb-6">
            Welcome, {{ auth('securityguard')->user()->first_name }} {{ auth('securityguard')->user()->last_name }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
            <!-- QR Scanner Card -->
            <a href="{{ route('security.scanner.show') }}" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow no-underline">
                <h2 class="text-xl font-semibold text-blue-600 mb-2">📷 QR Scanner</h2>
                <p class="text-gray-600">Scan QR codes to log entry and exit of users</p>
            </a>
            <br>
            <!-- Dashboard Info Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">ℹ️ Account Info</h2>
                <p class="text-gray-600">Email: {{ auth('securityguard')->user()->email }}</p>
                <p class="text-gray-600">Status: {{ auth('securityguard')->user()->status }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('security.logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                Logout
            </button>
        </form>
    </div>
</body>
</html>
