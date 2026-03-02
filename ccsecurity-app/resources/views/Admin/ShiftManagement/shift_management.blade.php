<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management - Admin</title>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>🕐 Shift Management</h1>
            <a href="{{ route('admin.dashboard') }}">← Back to Dashboard</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        <!-- Assign Shift Section -->
        <div>
            <h3>📅 Assign New Shift</h3>
            <button type="button" onclick="document.getElementById('assignShiftModal').style.display = 'block'">
                + Assign Shift
            </button>
        </div>

        <!-- Upcoming Shifts Table -->
        <div>
            <h3>📋 Upcoming Shifts</h3>
            @if($shifts->count() > 0)
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Security Guard</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                        <tr>
                            <td><strong>#{{ $shift->id }}</strong></td>
                            <td>{{ $shift->securityGuardUser->first_name ?? 'N/A' }} {{ $shift->securityGuardUser->last_name ?? '' }}</td>
                            <td>{{ $shift->shift_date->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hrs</td>
                            <td>
                                <span>
                                    {{ ucfirst($shift->status) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('admin.guard.shifts', $shift->security_guard_user_id) }}">View Guard</a>
                                    <form method="POST" action="{{ route('admin.shift.delete', $shift->id) }}" onsubmit="return confirm('Delete this shift?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
            <div>
                {{ $shifts->links() }}
            </div>
            @endif
            @else
            <p>📭 No upcoming shifts scheduled.</p>
            @endif
        </div>
    </div>

    <!-- Assign Shift Modal -->
    <div id="assignShiftModal" style="display:none;">
        <div>
            <div>
                <h3>📅 Assign New Shift</h3>
                <button type="button" onclick="document.getElementById('assignShiftModal').style.display = 'none'">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.assign.shift') }}" id="assignShiftForm">
                @csrf
                <div>
                    <div>
                        <label for="security_guard_user_id">Security Guard *</label>
                        <select id="security_guard_user_id" name="security_guard_user_id" required>
                            <option value="">Select Guard</option>
                            @foreach($securityGuards as $guard)
                            <option value="{{ $guard->id }}">
                                {{ $guard->first_name }} {{ $guard->last_name }} ({{ $guard->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="recurring_type">Shift Type *</label>
                        <select id="recurring_type" name="recurring_type" required onchange="toggleRecurringOptions()">
                            <option value="single">Single Day</option>
                            <option value="recurring">Recurring</option>
                        </select>
                    </div>
                </div>

                <!-- Single Day Option -->
                <div id="single_day_option">
                    <div>
                        <label for="shift_date">Shift Date *</label>
                        <input 
                            type="date" 
                            id="shift_date" 
                            name="shift_date" 
                            min="{{ today()->format('Y-m-d') }}"
                        >
                    </div>
                </div>

                <!-- Recurring Options -->
                <div id="recurring_options" style="display: none;">
                    <div>
                        <div>
                            <label for="recurring_start_date">Start Date *</label>
                            <input 
                                type="date" 
                                id="recurring_start_date" 
                                name="shift_date" 
                                min="{{ today()->format('Y-m-d') }}"
                            >
                        </div>
                        <div>
                            <label for="recurring_end_date">End Date *</label>
                            <input 
                                type="date" 
                                id="recurring_end_date" 
                                name="recurring_end_date" 
                                min="{{ today()->format('Y-m-d') }}"
                            >
                        </div>
                    </div>
                    <div>
                        <label>Repeat On *</label>
                        <div>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="0">
                                <span>Sun</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="1">
                                <span>Mon</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="2">
                                <span>Tue</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="3">
                                <span>Wed</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="4">
                                <span>Thu</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="5">
                                <span>Fri</span>
                            </label>
                            <label>
                                <input type="checkbox" name="recurring_days[]" value="6">
                                <span>Sat</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <button type="button" onclick="selectWeekdays()">Weekdays (Mon-Fri)</button>
                        <button type="button" onclick="selectWeekend()">Weekends (Sat-Sun)</button>
                        <button type="button" onclick="clearDays()">Clear</button>
                    </div>
                </div>

                <div>
                    <div>
                        <label for="start_time">Start Time *</label>
                        <input 
                            type="time" 
                            id="start_time" 
                            name="start_time" 
                            required
                        >
                    </div>
                    <div>
                        <label for="end_time">End Time *</label>
                        <input 
                            type="time" 
                            id="end_time" 
                            name="end_time" 
                            required
                        >
                    </div>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('assignShiftModal').style.display = 'none'">
                        Cancel
                    </button>
                    <button type="submit">
                        Assign Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleRecurringOptions() {
            const recurringType = document.getElementById('recurring_type').value;
            const singleDayOption = document.getElementById('single_day_option');
            const recurringOptions = document.getElementById('recurring_options');
            const shiftDateInput = document.getElementById('shift_date');

            if (recurringType === 'single') {
                singleDayOption.style.display = 'block';
                recurringOptions.style.display = 'none';
                shiftDateInput.required = true;
            } else {
                singleDayOption.style.display = 'none';
                recurringOptions.style.display = 'block';
                shiftDateInput.required = false;
            }
        }

        function selectWeekdays() {
            document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => {
                cb.checked = ['1', '2', '3', '4', '5'].includes(cb.value);
            });
        }

        function selectWeekend() {
            document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => {
                cb.checked = ['0', '6'].includes(cb.value);
            });
        }

        function clearDays() {
            document.querySelectorAll('input[name="recurring_days[]"]').forEach(cb => {
                cb.checked = false;
            });
        }

        // Set default min dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('input[type="date"]').forEach(input => {
                input.min = today;
            });
        });
    </script>
</body>
</html>
