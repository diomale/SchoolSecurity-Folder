<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connected Parents - Inside User</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn-back {
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back:hover { background: #5a6268; }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        .info-box a {
            color: #1976d2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Connected Parents/Guardians</h1>
            <div class="nav-links">
                <a href="{{ route('insideuser.dashboard') }}">← Back to Dashboard</a>
                <a href="{{ route('insideuser.connection.requests') }}">Connection Requests</a>
            </div>
        </div>

        @if($connectedParents->count() > 0)
        <p style="color: #666;">These people can see your entry and exit records at school.</p>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Relationship</th>
                    <th>Connected Since</th>
                </tr>
            </thead>
            <tbody>
                @foreach($connectedParents as $parent)
                <tr>
                    <td><strong>{{ $parent->fullname ?? 'N/A' }}</strong></td>
                    <td>{{ $parent->email ?? 'N/A' }}</td>
                    <td>{{ $parent->phone_number ?? 'N/A' }}</td>
                    <td>{{ $parent->pivot->relationship }}</td>
                    <td>{{ \Carbon\Carbon::parse($parent->pivot->approved_at)->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="info-box">
            <h3>No Connected Parents Yet</h3>
            <p>You haven't accepted any parent connection requests.</p>
            <p>When someone requests to connect with you, you'll see it in your <a href="{{ route('insideuser.connection.requests') }}">Connection Requests</a> page.</p>
        </div>
        @endif
    </div>
</body>
</html>
