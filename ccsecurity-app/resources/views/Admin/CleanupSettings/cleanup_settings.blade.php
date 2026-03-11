<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanup Settings - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"> Auto-Delete Cleanup Settings</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 no-underline">
                ← Back to Dashboard
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <!-- Global Status Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800"> Global Auto-Delete Status</h2>
            
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-lg font-medium text-gray-700">Master Auto-Delete Switch</p>
                    <p class="text-sm text-gray-500">
                        @if($globalSettings->auto_delete_enabled)
                            Currently <span class="text-green-600 font-semibold">ENABLED</span>
                        @else
                            Currently <span class="text-red-600 font-semibold">DISABLED</span>
                        @endif
                    </p>
                </div>
                
                <button onclick="openPasswordModal('toggle-global')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                     Toggle Global Setting
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <p class="text-sm text-blue-800">
                    <strong> Note:</strong> The global switch controls whether scheduled cleanup runs automatically.
                    Individual table settings control which tables are cleaned and their retention periods.
                    Scheduled cleanup runs <strong>daily at midnight</strong>.
                </p>
            </div>
        </div>

        <!-- Table-Specific Settings -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @foreach($tableSettings as $tableName => $tableData)
                @php
                    $settings = $tableData['settings'];
                    $label = $tableData['label'];
                    $tableStats = $stats[$tableName];
                @endphp
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800"> {{ $label }}</h3>
                    
                    <!-- Statistics -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 border rounded p-3 text-center">
                            <p class="text-xs text-gray-600">Total Records</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $tableStats['total'] }}</p>
                        </div>
                        <div class="bg-orange-50 border rounded p-3 text-center">
                            <p class="text-xs text-orange-600">Older Than 30 Days</p>
                            <p class="text-2xl font-bold text-orange-800">{{ $tableStats['older_than_30_days'] }}</p>
                        </div>
                    </div>

                    <!-- Current Settings -->
                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <p class="text-sm">
                            <span class="font-medium">Auto-Delete:</span>
                            <span class="{{ $settings->auto_delete_enabled ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                {{ $settings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </p>
                        <p class="text-sm mt-1">
                            <span class="font-medium">Retention:</span>
                            <span class="text-blue-600 font-semibold">{{ $settings->retention_days }} days</span>
                            @if($settings->retention_days == 0)
                                <span class="text-orange-600 text-xs">(Delete all records)</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            Last cleanup: {{ $settings->last_cleanup_date ? $settings->last_cleanup_date->format('M d, Y h:i A') : 'Never' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button onclick="openEditModal('{{ $tableName }}', {{ $settings->auto_delete_enabled ? 1 : 0 }}, {{ $settings->retention_days }})" 
                            class="flex-1 px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                             Edit Settings
                        </button>
                        <button onclick="openRunModal('{{ $tableName }}', {{ $settings->retention_days }})" 
                            class="flex-1 px-3 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600">
                             Run Now
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Schedule Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800"> Scheduled Cleanup</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-700">Schedule:</span>
                    <span class="font-medium text-gray-900">Every day at 12:00 AM</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-700">Commands:</span>
                    <div class="text-right">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">notifications:cleanup-old</code><br>
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">visitrequests:cleanup-old</code><br>
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">shiftlogs:cleanup-old</code><br>
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">shifts:cleanup-old</code>
                    </div>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-700">Global Auto-Delete:</span>
                    <span class="font-medium {{ $globalSettings->auto_delete_enabled ? 'text-green-600' : 'text-red-600' }}">
                        {{ $globalSettings->auto_delete_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-600">
                    <strong> Production Setup:</strong> Windows Task Scheduler runs daily. 
                    Commands execute cleanup daily. Each table uses its own retention setting.
                </p>
            </div>
        </div>
    </div>

    <!-- Password Modal for Global Toggle -->
    <div id="passwordModal-toggle-global" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4"> Confirm Global Toggle</h3>
            <p class="text-gray-600 mb-4">Enter your admin password to toggle the global auto-delete setting.</p>
            
            <form action="{{ route('admin.cleanup.toggle-global') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Password</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your password">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Confirm
                    </button>
                    <button type="button" onclick="closePasswordModal('toggle-global')" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Settings Modal -->
    <div id="editSettingsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4"> Edit Cleanup Settings</h3>
            <p class="text-gray-600 mb-4">Enter your admin password to update settings.</p>
            
            <form action="{{ route('admin.cleanup.update-table') }}" method="POST">
                @csrf
                <input type="hidden" name="table_name" id="edit-table-name">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Delete</label>
                    <select name="auto_delete_enabled" id="edit-auto-delete" 
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retention Period (days)</label>
                    <input type="number" name="retention_days" id="edit-retention-days" 
                        min="0" max="365" required
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">0 = Delete all records (no retention)</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Password</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your password">
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Save Changes
                    </button>
                    <button type="button" onclick="closeEditModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Run Cleanup Modal -->
    <div id="runCleanupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4"> Run Manual Cleanup</h3>
            <p class="text-gray-600 mb-4">Enter your admin password to run cleanup now.</p>
            
            <form action="{{ route('admin.cleanup.run-now') }}" method="POST">
                @csrf
                <input type="hidden" name="table_name" id="run-table-name">
                <input type="hidden" name="retention_days" id="run-retention-days">
                
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong> Warning:</strong> This will permanently delete records 
                        <span id="run-retention-text"></span>
                    </p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Password</label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your password">
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                         Run Cleanup
                    </button>
                    <button type="button" onclick="closeRunModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(type) {
            document.getElementById('passwordModal-' + type).classList.remove('hidden');
        }

        function closePasswordModal(type) {
            document.getElementById('passwordModal-' + type).classList.add('hidden');
        }

        function openEditModal(tableName, autoDelete, retentionDays) {
            document.getElementById('edit-table-name').value = tableName;
            document.getElementById('edit-auto-delete').value = autoDelete;
            document.getElementById('edit-retention-days').value = retentionDays;
            document.getElementById('editSettingsModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editSettingsModal').classList.add('hidden');
        }

        function openRunModal(tableName, retentionDays) {
            document.getElementById('run-table-name').value = tableName;
            document.getElementById('run-retention-days').value = retentionDays;
            const retentionText = retentionDays == 0 
                ? 'completely (ALL records)' 
                : 'older than ' + retentionDays + ' days';
            document.getElementById('run-retention-text').textContent = retentionText;
            document.getElementById('runCleanupModal').classList.remove('hidden');
        }

        function closeRunModal() {
            document.getElementById('runCleanupModal').classList.add('hidden');
        }

        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
