<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry/Exit Logs - Security Guard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
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
        }
        .stats-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
        }
        .stat-entries { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-exits { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-inside { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card h3 { margin: 0; font-size: 14px; opacity: 0.9; }
        .stat-card p { margin: 10px 0 0; font-size: 32px; font-weight: bold; }
        .currently-inside-section {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .currently-inside-section h2 {
            margin-top: 0;
            color: #1976d2;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .badge-count {
            background: #f44336;
            color: white;
            border-radius: 50%;
            padding: 2px 10px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        .filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filters label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .filters input, .filters select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }
        .filters button {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filters button:hover {
            background: #0056b3;
        }
        .filters a {
            color: #dc3545;
            text-decoration: none;
        }
        .entry-badge {
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .exit-badge {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .pagination {
            margin-top: 20px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .no-inside {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1> Entry/Exit Logs</h1>
            <a href="{{ route('security.dashboard') }}" style="color: #007bff; text-decoration: none;">← Back to Dashboard</a>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card stat-entries">
                <h3> Entries Today</h3>
                <p>{{ $totalEntriesToday }}</p>
            </div>
            <div class="stat-card stat-exits">
                <h3> Exits Today</h3>
                <p>{{ $totalExitsToday }}</p>
            </div>
            <div class="stat-card stat-inside">
                <h3> Currently Inside</h3>
                <p>{{ $currentlyInsideCount }}</p>
            </div>
        </div>

        <!-- Currently Inside Section -->
        <div class="currently-inside-section">
            <h2>
                 People Currently Inside School
                @if($currentlyInsidePeople->count() > 0)
                    <span class="badge-count">{{ $currentlyInsidePeople->count() }}</span>
                @endif
            </h2>
            
            @if($currentlyInsidePeople->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Entry Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentlyInsidePeople as $person)
                    <tr>
                        <td><strong>{{ $person['fullname'] ?? 'Unknown' }}</strong></td>
                        <td>{{ $person['email'] ?? 'N/A' }}</td>
                        <td>
                            @if(isset($person['role']) && $person['role'])
                                <span style="background: #e3f2fd; color: #1976d2; padding: 4px 10px; border-radius: 4px; font-size: 12px;">{{ ucfirst($person['role']) }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="entry-badge">✓ Entry</span>
                            {{ $person['scan_at'] ? \Carbon\Carbon::parse($person['scan_at'])->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-inside">
                <p>📭 No one is currently inside the school premises.</p>
            </div>
            @endif
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="{{ route('security.entry.logs') }}">
                <div>
                    <label for="search">🔍 Search</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        placeholder="Name or Email..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div>
                    <label for="scan_type">Filter by Type</label>
                    <select id="scan_type" name="scan_type">
                        <option value="">All Types</option>
                        <option value="entry" {{ request('scan_type') == 'entry' ? 'selected' : '' }}>Entry</option>
                        <option value="exit" {{ request('scan_type') == 'exit' ? 'selected' : '' }}>Exit</option>
                    </select>
                </div>
                <div>
                    <label for="date">Filter by Date</label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ request('date') }}"
                    >
                </div>
                <div>
                    <button type="submit">Apply Filters</button>
                </div>
                @if(request('search') || request('scan_type') || request('date'))
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('security.entry.logs') }}">Clear Filters</a>
                </div>
                @endif
            </form>
        </div>

        <!-- Logs Table -->
        <div>
            <h2 style="margin-bottom: 15px;">📜 Recent Entry/Exit Logs</h2>
            @if($logs->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Person Name</th>
                        <th>Type</th>
                        <th>Scan Time</th>
                        <th>Scanned By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            @if($log->insideUser)
                                {{ $log->insideUser->fullname }}
                            @elseif($log->outsideUser)
                                {{ $log->outsideUser->fullname }} <span style="color: #999; font-size: 12px;">(Visitor)</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($log->scan_type === 'entry')
                                <span class="entry-badge">✓ Entry</span>
                            @elseif($log->scan_type === 'exit')
                                <span class="exit-badge">✗ Exit</span>
                            @else
                                <span>{{ $log->scan_type }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $log->scan_at ? \Carbon\Carbon::parse($log->scan_at)->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                        <td>
                            @if($log->securityGuardUser)
                                {{ $log->securityGuardUser->fullname ?? ($log->securityGuardUser->first_name . ' ' . $log->securityGuardUser->last_name) }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="pagination">
                {{ $logs->links() }}
            </div>
            @endif
            @else
            <div class="empty-state">
                <p>📭 No entry/exit logs found matching your criteria.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
