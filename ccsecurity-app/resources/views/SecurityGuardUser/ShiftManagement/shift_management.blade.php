<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management - Security Guard</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1> Shift Management</h1>
            <a href="{{ route('security.dashboard') }}">← Back to Dashboard</a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div>
            {{ session('error') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div>
            <div>
                <h3> Shifts This Week</h3>
                <p>{{ $totalShiftsThisWeek }}</p>
            </div>
            <div>
                <h3> Hours This Week</h3>
                <p>{{ number_format($totalHoursThisWeek, 1) }}</p>
            </div>
        </div>

        <!-- Current Shift Status -->
        <div>
            @if($currentShiftLog)
            <div>
                <h2> Currently On Shift</h2>
                <div>{{ $currentShiftLog->clock_in_time->format('h:i A') }}</div>
                <div>Clocked in at {{ $currentShiftLog->clock_in_time->format('M d, Y') }}</div>
                <div>
                    <form method="POST" action="{{ route('security.clock.out') }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Clock out from your shift?')">
                             Clock Out
                        </button>
                    </form>
                    @if($currentShiftLog->id)
                    <button type="button" onclick="document.getElementById('handover-form').style.display='block'">
                         Add Handover Note
                    </button>
                    @endif
                </div>
            </div>

            <!-- Handover Note Form -->
            <div id="handover-form" style="display: none;">
                <h3> Handover Note for Next Guard</h3>
                <form method="POST" action="{{ route('security.submit.handover') }}">
                    @csrf
                    <input type="hidden" name="shift_log_id" value="{{ $currentShiftLog->id }}">
                    <textarea 
                        name="handover_note" 
                        placeholder="Write any important information for the next guard..."
                        required
                    ></textarea>
                    <div>
                        <button type="button" onclick="document.getElementById('handover-form').style.display='none'">
                            Cancel
                        </button>
                        <button type="submit">
                            Submit Handover
                        </button>
                    </div>
                </form>
            </div>

            @else
            <div>
                <h2> Not Currently On Shift</h2>
                <div>{{ today()->format('l, F d, Y') }}</div>
                <div>
                    <form method="POST" action="{{ route('security.clock.in') }}">
                        @csrf
                        <button type="submit">
                             Clock In
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Today's Scheduled Shift -->
        @if($todayShift)
        <div>
            <h3> Today's Scheduled Shift</h3>
            <div>
                <p><strong>Date:</strong> {{ $todayShift->shift_date->format('F d, Y') }}</p>
                <p><strong>Shift Time:</strong> {{ \Carbon\Carbon::parse($todayShift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($todayShift->end_time)->format('h:i A') }}</p>
                <p><strong>Status:</strong> 
                    <span>
                        {{ ucfirst($todayShift->status) }}
                    </span>
                </p>
            </div>
        </div>
        @endif

        <!-- Recent Shift History -->
        <div>
            <h3> Recent Shift History</h3>
            @if($recentShiftLogs->count() > 0)
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentShiftLogs as $log)
                        <tr>
                            <td>{{ $log->clock_in_time->format('M d, Y') }}</td>
                            <td>{{ $log->clock_in_time->format('h:i A') }}</td>
                            <td>{{ $log->clock_out_time->format('h:i A') }}</td>
                            <td>{{ $log->clock_in_time->diffInHours($log->clock_out_time) }} hrs</td>
                            <td>
                                <span>Completed</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div>
                <p> No shift history available yet.</p>
            </div>
            @endif
        </div>

        <!-- Navigation Links -->
        <div>
            <a href="{{ route('security.shift.schedule') }}"> View Schedule</a>
            <a href="{{ route('security.shift.history') }}"> Full History</a>
        </div>
    </div>
</body>
</html>
