<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $guard->first_name }} {{ $guard->last_name }} - Shifts</title>
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
        .guard-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .guard-info h2 {
            margin: 0 0 10px 0;
        }
        .guard-info p {
            margin: 5px 0;
            opacity: 0.9;
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
        .shift-card.past {
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
            margin-bottom: 10px;
        }
        .badge-scheduled {
            background: #fff3cd;
            color: #856404;
        }
        .badge-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .no-shifts {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>👤 Guard Shift Schedule</h1>
            <a href="{{ $backUrl }}" class="back-link">← Back</a>
        </div>

        <!-- Guard Info -->
        <div class="guard-info">
            <h2>{{ $guard->first_name }} {{ $guard->last_name }}</h2>
            <p>📧 {{ $guard->email }}</p>
            <p>🆔 ID: {{ $guard->id }}</p>
        </div>

        <!-- Shifts List -->
        @if($shifts->count() > 0)
            @foreach($shifts as $shift)
            <div class="shift-card {{ $shift->shift_date->isToday() ? 'today' : ($shift->shift_date->isPast() ? 'past' : '') }}">
                <div class="shift-info">
                    <p class="date">
                        📆 {{ $shift->shift_date->format('l, F d, Y') }}
                        @if($shift->shift_date->isToday())
                            <span class="badge badge-scheduled">Today</span>
                        @endif
                    </p>
                    <p class="time">
                        🕐 {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                    </p>
                    <p>
                        ⏱️ Duration: {{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hours
                    </p>
                </div>
                <div class="shift-status">
                    <span class="badge badge-{{ $shift->status }}">
                        {{ ucfirst($shift->status) }}
                    </span>
                    <br>
                    <form method="POST" action="{{ route('admin.shift.delete', $shift->id) }}" style="display: inline;" onsubmit="return confirm('Delete this shift?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Shift</button>
                    </form>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            @if($shifts->hasPages())
            <div class="pagination">
                {{ $shifts->links() }}
            </div>
            @endif
        @else
            <div class="no-shifts">
                <h3>📭 No Shifts Assigned</h3>
                <p>This guard doesn't have any scheduled shifts yet.</p>
            </div>
        @endif
    </div>
</body>
</html>
