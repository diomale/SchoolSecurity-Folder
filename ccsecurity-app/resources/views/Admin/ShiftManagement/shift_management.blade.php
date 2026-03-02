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

        <!-- Messages -->
        @if(session('success'))
        <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 10px;">
            {{ session('error') }}
        </div>
        @endif

        <!-- Assign Shift Section -->
        <div>
            <h3>📅 Assign New Shift</h3>
            <button type="button" onclick="openAssignShiftModal()">
                + Assign Shift
            </button>
        </div>

        <!-- Upcoming Shifts Table -->
        <div>
            <h3>📋 Upcoming Shifts</h3>
            @if($shifts->count() > 0)
            <div>
                <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
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
                            <td>{{ $shift->id }}</td>
                            <td>{{ $shift->securityGuardUser->first_name ?? 'N/A' }} {{ $shift->securityGuardUser->last_name ?? '' }}</td>
                            <td>{{ $shift->shift_date->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hrs</td>
                            <td>{{ ucfirst($shift->status) }}</td>
                            <td>
                                <a href="{{ route('admin.guard.shifts', $shift->security_guard_user_id) }}">View Guard</a>
                                <form action="{{ route('admin.shift.delete', $shift->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this shift?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
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
            <p>No upcoming shifts scheduled.</p>
            @endif
        </div>
    </div>

    <!-- Assign Shift Modal -->
    <div id="assignShiftModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 600px; border-radius: 8px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                <h3 style="margin: 0;">📅 Assign New Shift</h3>
                <button type="button" onclick="closeAssignShiftModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.assign.shift') }}">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Security Guard *</label>
                        <select id="security_guard_user_id" name="security_guard_user_id" required style="width: 100%; padding: 8px;">
                            <option value="">Select Guard</option>
                            @foreach($securityGuards as $guard)
                            <option value="{{ $guard->id }}">
                                {{ $guard->first_name }} {{ $guard->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Shift Type *</label>
                        <select id="recurring_type" name="recurring_type" required onchange="toggleRecurringOptions()" style="width: 100%; padding: 8px;">
                            <option value="single">Single Day</option>
                            <option value="recurring">Recurring</option>
                        </select>
                    </div>
                </div>

                <!-- Single Day Option -->
                <div id="single_day_option" style="margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Shift Date *</label>
                        <input
                            type="date"
                            id="shift_date"
                            name="shift_date"
                            min="{{ today()->format('Y-m-d') }}"
                            style="width: 100%; padding: 8px;"
                        >
                    </div>
                </div>

                <!-- Recurring Options -->
                <div id="recurring_options" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Start Date *</label>
                            <input
                                type="date"
                                id="recurring_start_date"
                                name="shift_date"
                                min="{{ today()->format('Y-m-d') }}"
                                style="width: 100%; padding: 8px;"
                            >
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">End Date *</label>
                            <input
                                type="date"
                                id="recurring_end_date"
                                name="recurring_end_date"
                                min="{{ today()->format('Y-m-d') }}"
                                style="width: 100%; padding: 8px;"
                            >
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: bold;">Repeat On *</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="0"> Sun
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="1"> Mon
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="2"> Tue
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="3"> Wed
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="4"> Thu
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="5"> Fri
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="6"> Sat
                            </label>
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <button type="button" onclick="selectWeekdays()" style="margin-right: 5px;">Weekdays (Mon-Fri)</button>
                        <button type="button" onclick="selectWeekend()" style="margin-right: 5px;">Weekends (Sat-Sun)</button>
                        <button type="button" onclick="clearDays()">Clear</button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Start Time *</label>
                        <input
                            type="time"
                            id="start_time"
                            name="start_time"
                            required
                            style="width: 100%; padding: 8px;"
                        >
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">End Time *</label>
                        <input
                            type="time"
                            id="end_time"
                            name="end_time"
                            required
                            style="width: 100%; padding: 8px;"
                        >
                    </div>
                </div>
                <div style="text-align: right;">
                    <button type="button" onclick="closeAssignShiftModal()" style="margin-right: 10px;">Cancel</button>
                    <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">Assign Shift</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAssignShiftModal() {
            document.getElementById('assignShiftModal').style.display = 'block';
        }

        function closeAssignShiftModal() {
            document.getElementById('assignShiftModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('assignShiftModal');
            if (event.target == modal) {
                closeAssignShiftModal();
            }
        }

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
