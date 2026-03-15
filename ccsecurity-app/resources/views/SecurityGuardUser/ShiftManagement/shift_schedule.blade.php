<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Schedule - Security Guard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
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
        .shift-card {
            background: white;
            border-left: 5px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .shift-card.today {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
        }
        .shift-card.scheduled {
            border-left-color: #ffc107;
        }
        .shift-card.completed {
            border-left-color: #6c757d;
            opacity: 0.8;
        }
        .shift-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .shift-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .shift-info .date {
            font-size: 18px;
            font-weight: 600;
            color: #007bff;
        }
        .shift-info .time {
            font-size: 16px;
            color: #28a745;
            font-weight: 500;
        }
        .shift-status {
            text-align: right;
        }
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-scheduled {
            background: #fff3cd;
            color: #856404;
        }
        .badge-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-today {
            background: #d4edda;
            color: #155724;
        }
        .no-shifts {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .no-shifts h3 {
            margin-bottom: 10px;
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
        .legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        .legend-color.today { background: #28a745; }
        .legend-color.scheduled { background: #ffc107; }
        .legend-color.completed { background: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1> Shift Schedule</h1>
            <a href="{{ route('security.shift.management') }}" class="back-link">← Back to Shift Management</a>
        </div>

        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color today"></div>
                <span>Today</span>
            </div>
            <div class="legend-item">
                <div class="legend-color scheduled"></div>
                <span>Scheduled</span>
            </div>
            <div class="legend-item">
                <div class="legend-color completed"></div>
                <span>Completed</span>
            </div>
        </div>

        <!-- Upcoming Shifts -->
        @if($upcomingShifts->count() > 0)
            @foreach($upcomingShifts as $shift)
            <div class="shift-card {{ $shift->shift_date->isToday() ? 'today' : 'scheduled' }}">
                <div class="shift-info">
                    <p class="date">
                         {{ $shift->shift_date->format('l, F d, Y') }}
                        @if($shift->shift_date->isToday())
                            <span class="badge badge-today">Today</span>
                        @endif
                    </p>
                    <p class="time">
                         {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                    </p>
                    <p>
                         Duration: {{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hours
                    </p>
                </div>
                <div class="shift-status">
                    <span class="badge badge-{{ $shift->status }}">
                        {{ ucfirst($shift->status) }}
                    </span>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            @if($upcomingShifts->hasPages())
            <div class="pagination">
                {{ $upcomingShifts->links() }}
            </div>
            @endif
        @else
            <div class="no-shifts">
                <h3> No Upcoming Shifts</h3>
                <p>You don't have any scheduled shifts at the moment.</p>
            </div>
        @endif
    </div>
</body>
</html>
