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

        <!-- Search and Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <form method="GET" action="{{ route('admin.shift.management') }}" style="display: flex; gap: 10px;">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by guard name, date, or status..." 
                    value="{{ request('search') }}"
                    style="width: 300px; padding: 8px;"
                >
                <button type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('admin.shift.management') }}" style="align-self: center;">Clear</a>
                @endif
            </form>

            <button type="button" onclick="openAssignShiftModal()" style="background: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px;">
                + Assign Shift
            </button>
        </div>

        <!-- Shifts List Section -->
        <div>
            <h3>📋 Shifts List</h3>
            
            <div style="margin-bottom: 10px;">
                <button type="button" onclick="submitBulkAction('bulk-delete-form', true)" id="bulk-delete-btn" disabled style="background-color: #fff0f0; color: #dc3545; border: 1px solid #dc3545; padding: 5px 10px; cursor: pointer;">
                    Bulk Delete Selected Shifts
                </button>
            </div>

            <!-- Hidden Bulk Delete Form -->
            <form id="bulk-delete-form" action="{{ route('admin.shift.bulk-delete') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            @if($shifts->count() > 0)
            <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
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
                        <td><input type="checkbox" value="{{ $shift->id }}" class="shift-checkbox"></td>
                        <td>{{ $shift->id }}</td>
                        <td>{{ $shift->securityGuardUser->first_name ?? 'N/A' }} {{ $shift->securityGuardUser->last_name ?? '' }}</td>
                        <td>{{ $shift->shift_date->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->diffInHours(\Carbon\Carbon::parse($shift->end_time)) }} hrs</td>
                        <td>{{ ucfirst($shift->status) }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.guard.shifts', $shift->security_guard_user_id) }}">View Guard</a>
                                <form action="{{ route('admin.shift.delete', $shift->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this shift?')">
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

            <div style="margin-top: 20px;">
                {{ $shifts->appends(request()->query())->links() }}
            </div>
            @else
            <p style="text-align: center; padding: 20px; border: 1px solid #ddd;">No shifts found.</p>
            @endif
        </div>
    </div>

    <!-- Assign Shift Modal (unchanged modal HTML ...) -->
    <div id="assignShiftModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 600px; border-radius: 8px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                <h3 style="margin: 0;">📅 Assign New Shift</h3>
                <button type="button" onclick="closeAssignShiftModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>

            @if ($errors->any())
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 10px;">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.assign.shift') }}">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Security Guard *</label>
                        <select id="security_guard_user_id" name="security_guard_user_id" required style="width: 100%; padding: 8px;">
                            <option value="">Select Guard</option>
                            @foreach($securityGuards as $guard)
                            <option value="{{ $guard->id }}" {{ old('security_guard_user_id') == $guard->id ? 'selected' : '' }}>
                                {{ $guard->first_name }} {{ $guard->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Shift Type *</label>
                        <select id="recurring_type" name="recurring_type" required onchange="toggleRecurringOptions()" style="width: 100%; padding: 8px;">
                            <option value="single" {{ old('recurring_type') === 'single' ? 'selected' : '' }}>Single Day</option>
                            <option value="recurring" {{ old('recurring_type') === 'recurring' ? 'selected' : '' }}>Recurring</option>
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
                            value="{{ old('shift_date') }}"
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
                                name="shift_date_recurring"
                                min="{{ today()->format('Y-m-d') }}"
                                value="{{ old('shift_date') }}"
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
                                value="{{ old('recurring_end_date') }}"
                                style="width: 100%; padding: 8px;"
                            >
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: bold;">Repeat On *</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="recurring_days[]" value="{{ $index }}"> {{ $day }}
                            </label>
                            @endforeach
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
                <div style="text-align: right; border-top: 1px solid #ddd; padding-top: 15px;">
                    <button type="button" onclick="closeAssignShiftModal()" style="margin-right: 10px; padding: 8px 15px;">Cancel</button>
                    <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px;">Assign Shift</button>
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

        function toggleRecurringOptions() {
            const recurringType = document.getElementById('recurring_type').value;
            const singleDayOption = document.getElementById('single_day_option');
            const recurringOptions = document.getElementById('recurring_options');
            
            if (recurringType === 'single') {
                singleDayOption.style.display = 'block';
                recurringOptions.style.display = 'none';
            } else {
                singleDayOption.style.display = 'none';
                recurringOptions.style.display = 'block';
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

        // Bulk Delete Functions
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.shift-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkDeleteButton();
        });

        document.querySelectorAll('.shift-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.shift-checkbox:checked').length;
            document.getElementById('bulk-delete-btn').disabled = checkedCount === 0;
        }

        function submitBulkAction(formId, isDelete = false) {
            const checkedIds = Array.from(document.querySelectorAll('.shift-checkbox:checked')).map(cb => cb.value);
            const form = document.getElementById(formId);
            
            if (isDelete) {
                if (!confirm(`Are you sure you want to delete ${checkedIds.length} selected shifts?`)) return;
            }

            form.querySelectorAll('input[name="shift_ids[]"]').forEach(el => el.remove());

            checkedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'shift_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.submit();
        }

        // Initialize min dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('input[type="date"]').forEach(input => {
                input.min = today;
            });
            
            toggleRecurringOptions();
        });
    </script>
</body>
</html>