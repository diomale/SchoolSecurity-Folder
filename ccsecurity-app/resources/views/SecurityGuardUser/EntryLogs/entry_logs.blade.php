<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry/Exit Logs - Security Guard</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>📋 Entry/Exit Logs</h1>
            <a href="{{ route('security.dashboard') }}">← Back to Dashboard</a>
        </div>

        <!-- Statistics Cards -->
        <div>
            <div>
                <h3>📥 Entries Today</h3>
                <p>{{ $totalEntriesToday }}</p>
            </div>
            <div>
                <h3>📤 Exits Today</h3>
                <p>{{ $totalExitsToday }}</p>
            </div>
            <div>
                <h3>👥 Currently Inside</h3>
                <p>{{ $currentlyInside }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div>
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
                <div>
                    <a href="{{ route('security.entry.logs') }}">Clear</a>
                </div>
                @endif
            </form>
        </div>

        <!-- Logs Table -->
        <div>
            @if($logs->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Person Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Scan Time</th>
                        <th>Scanned By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td><strong>#{{ $log->id }}</strong></td>
                        <td>{{ $log->insideUser->fullname ?? 'N/A' }}</td>
                        <td>{{ $log->insideUser->email ?? 'N/A' }}</td>
                        <td>
                            @if($log->scan_type === 'entry')
                                <span>✓ Entry</span>
                            @else
                                <span>✗ Exit</span>
                            @endif
                        </td>
                        <td>
                            {{ $log->scan_at ? \Carbon\Carbon::parse($log->scan_at)->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                        <td>
                            {{ $log->securityGuardUser->first_name ?? 'N/A' }} {{ $log->securityGuardUser->last_name ?? '' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div>
                {{ $logs->links() }}
            </div>
            @endif
            @else
            <div>
                <p>📭 No entry/exit logs found matching your criteria.</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
