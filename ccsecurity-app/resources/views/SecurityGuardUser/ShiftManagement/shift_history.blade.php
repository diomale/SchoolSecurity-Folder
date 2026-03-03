<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift History - Security Guard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
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
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .back-link {
            color: #007bff;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .back-link:hover {
            background: #007bff;
            color: white;
        }
        .filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }
        .form-group {
            flex: 1;
            min-width: 150px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .summary-card .number {
            font-size: 42px;
            font-weight: bold;
            margin: 0;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .no-records {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 16px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
            transition: all 0.3s;
        }
        .pagination a:hover {
            background: #007bff;
            color: white;
        }
        .pagination .active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .handover-note {
            max-width: 300px;
            font-size: 13px;
            color: #666;
        }
        .handover-note truncated {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📜 Shift History</h1>
            <a href="{{ route('security.shift.management') }}" class="back-link">← Back to Shift Management</a>
        </div>

        <!-- Summary Card -->
        <div class="summary-card">
            <h3>⏱️ Total Hours Worked (Selected Period)</h3>
            <p class="number">{{ number_format($totalHours, 1) }}</p>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="{{ route('security.shift.history') }}">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input 
                        type="date" 
                        id="start_date" 
                        name="start_date" 
                        value="{{ request('start_date') }}"
                    >
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input 
                        type="date" 
                        id="end_date" 
                        name="end_date" 
                        value="{{ request('end_date') }}"
                    >
                </div>
                <div class="form-group" style="flex: 0;">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
                @if(request('start_date') || request('end_date'))
                <div class="form-group" style="flex: 0;">
                    <label>&nbsp;</label>
                    <a href="{{ route('security.shift.history') }}" class="btn btn-secondary">Clear</a>
                </div>
                @endif
            </form>
        </div>

        <!-- Shift History Table -->
        @if($shiftHistory->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Duration (hrs)</th>
                        <th>Handover Note</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shiftHistory as $log)
                    <tr>
                        <td>{{ $log->clock_in_time->format('M d, Y') }}</td>
                        <td>{{ $log->clock_in_time->format('h:i A') }}</td>
                        <td>{{ $log->clock_out_time->format('h:i A') }}</td>
                        <td><strong>{{ number_format($log->clock_in_time->diffInHours($log->clock_out_time), 1) }}</strong></td>
                        <td class="handover-note">
                            @if($log->handover_note)
                                {{ Str::limit($log->handover_note, 50) }}
                            @else
                                <em style="color: #999;">No note</em>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-completed">Completed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($shiftHistory->hasPages())
        <div class="pagination">
            {{ $shiftHistory->links() }}
        </div>
        @endif
        @else
        <div class="no-records">
            <p>📭 No shift history found matching your criteria.</p>
        </div>
        @endif
    </div>
</body>
</html>
