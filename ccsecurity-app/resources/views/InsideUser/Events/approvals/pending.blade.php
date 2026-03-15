<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - {{ $event->event_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d1ecf1; color: #0c5460; }
        .nav-link { color: #007bff; text-decoration: none; font-size: 14px; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .checkbox-cell { width: 40px; text-align: center; }
        .actions-bar { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab { padding: 10px 20px; border-radius: 4px; text-decoration: none; color: #666; background: #e9ecef; }
        .tab.active { background: #007bff; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: 100px auto; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial, sans-serif; resize: vertical; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <!-- Header -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <a href="{{ route('insideuser.events.show', $event->id) }}" class="nav-link" style="font-size: 12px;">← Back to Event</a>
                    <h1 style="margin: 5px 0 0 0; font-size: 24px;">{{ $event->event_name }} - Registration Approvals</h1>
                </div>
                <div>
                    <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-primary">View All Registrations</a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <h2 style="margin: 0 0 15px 0; font-size: 18px;">Approval Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card" style="background: #fff3cd;">
                    <div style="font-size: 24px; font-weight: bold; color: #856404;">{{ $statistics['pending_count'] }}</div>
                    <div style="font-size: 12px; color: #666;">Pending Approval</div>
                </div>
                <div class="stat-card" style="background: #d4edda;">
                    <div style="font-size: 24px; font-weight: bold; color: #155724;">{{ $statistics['approved_count'] }}</div>
                    <div style="font-size: 12px; color: #666;">Approved</div>
                </div>
                <div class="stat-card" style="background: #e3f2fd;">
                    <div style="font-size: 24px; font-weight: bold; color: #1976d2;">{{ $statistics['total_registrations'] }}</div>
                    <div style="font-size: 12px; color: #666;">Total Registrations</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <a href="#pending" class="tab active" onclick="showTab('pending')">Pending Approvals ({{ $statistics['pending_count'] }})</a>
            <a href="#approved" class="tab" onclick="showTab('approved')">Approved ({{ $statistics['approved_count'] }})</a>
        </div>

        <!-- Pending Approvals Section -->
        <div id="pending-section">
            @if($pendingRegistrations->count() > 0)
            <div class="card">
                <!-- Bulk Actions -->
                <form id="bulk-approve-form" action="{{ route('insideuser.events.approvals.bulk-approve', $event->id) }}" method="POST">
                    @csrf
                    <div class="actions-bar">
                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                        <label for="select-all" style="margin: 0; cursor: pointer;">Select All</label>
                        <button type="submit" class="btn btn-success btn-sm">Approve Selected</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showRejectModal()">Reject Selected</button>
                        <span id="selected-count" style="margin-left: auto; color: #666;">0 selected</span>
                    </div>
                </form>

                <form id="bulk-reject-form" action="{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="registration_ids" id="reject-ids">
                </form>

                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-cell">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>QR Code</th>
                            <th>Registered At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRegistrations as $reg)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" name="registration_ids[]" value="{{ $reg->id }}" class="reg-checkbox" onchange="updateSelectedCount()">
                            </td>
                            <td>{{ $reg->fullname }}</td>
                            <td>{{ $reg->email }}</td>
                            <td>{{ $reg->phone_number ?? 'N/A' }}</td>
                            <td><code style="font-size: 11px;">{{ $reg->qr_code }}</code></td>
                            <td>{{ $reg->created_at->format('M d, Y g:i A') }}</td>
                            <td>
                                <form action="{{ route('insideuser.events.approvals.approve', $reg->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" onclick="showSingleRejectModal({{ $reg->id }})">Reject</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div style="margin-top: 20px;">
                    {{ $pendingRegistrations->links() }}
                </div>
            </div>
            @else
            <div class="card">
                <div style="text-align: center; padding: 40px;">
                    <h3 style="color: #28a745; margin: 0 0 10px 0;">✓ All Caught Up!</h3>
                    <p style="color: #666;">No pending registrations awaiting your approval.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Approved Registrations Section -->
        <div id="approved-section" style="display: none;">
            @if($approvedRegistrations->count() > 0)
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>QR Code</th>
                            <th>Approved At</th>
                            <th>Email Sent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedRegistrations as $reg)
                        <tr>
                            <td>{{ $reg->fullname }}</td>
                            <td>{{ $reg->email }}</td>
                            <td>{{ $reg->phone_number ?? 'N/A' }}</td>
                            <td><code style="font-size: 11px;">{{ $reg->qr_code }}</code></td>
                            <td>{{ $reg->creator_approved_at ? $reg->creator_approved_at->format('M d, Y g:i A') : 'N/A' }}</td>
                            <td>
                                @if($reg->qr_emailed)
                                    <span class="badge badge-green">Sent</span>
                                @else
                                    <span class="badge badge-yellow">Not Sent</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div style="margin-top: 20px;">
                    {{ $approvedRegistrations->links() }}
                </div>
            </div>
            @else
            <div class="card">
                <div style="text-align: center; padding: 40px;">
                    <p style="color: #666;">No approved registrations yet.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="modal">
        <div class="modal-content">
            <h2 style="margin: 0 0 20px 0; font-size: 20px;">Reject Registration</h2>
            <form id="reject-form" method="POST">
                @csrf
                <input type="hidden" name="registration_ids" id="modal-reject-ids">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Rejection Reason (Optional)</label>
                    <textarea name="rejection_reason" rows="4" placeholder="Enter a reason for rejection..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn" style="background: #e9ecef; color: #666;" onclick="hideRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.reg-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.reg-checkbox:checked').length;
            document.getElementById('selected-count').textContent = checked + ' selected';
        }

        function showTab(tabName) {
            const pendingSection = document.getElementById('pending-section');
            const approvedSection = document.getElementById('approved-section');
            const tabs = document.querySelectorAll('.tab');

            if (tabName === 'pending') {
                pendingSection.style.display = 'block';
                approvedSection.style.display = 'none';
                tabs[0].classList.add('active');
                tabs[1].classList.remove('active');
            } else {
                pendingSection.style.display = 'none';
                approvedSection.style.display = 'block';
                tabs[0].classList.remove('active');
                tabs[1].classList.add('active');
            }
        }

        function showRejectModal() {
            const checked = Array.from(document.querySelectorAll('.reg-checkbox:checked')).map(cb => cb.value);
            if (checked.length === 0) {
                alert('Please select at least one registration to reject.');
                return;
            }
            document.getElementById('modal-reject-ids').value = checked.join(',');
            document.getElementById('bulk-reject-form').action = "{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}";
            document.getElementById('reject-modal').style.display = 'block';
        }

        function showSingleRejectModal(registrationId) {
            document.getElementById('modal-reject-ids').value = registrationId;
            document.getElementById('bulk-reject-form').action = "{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}";
            document.getElementById('reject-modal').style.display = 'block';
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reject-modal');
            if (event.target === modal) {
                hideRejectModal();
            }
        }
    </script>
</body>
</html>
