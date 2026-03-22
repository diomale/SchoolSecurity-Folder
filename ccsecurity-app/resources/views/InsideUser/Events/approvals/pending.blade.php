<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUserStyleFolder/insideuser_dashboard_style.css', 'resources/css/InsideUserStyleFolder/insideuser_style_events.css'])
    <style>
        .custom-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .custom-modal.active { display: flex; }
        .custom-modal-content { background: white; padding: 30px; border-radius: var(--radius-xl); width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .custom-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 15px; }
        .custom-modal-header h2 { font-size: 1.4rem; color: var(--text-main); margin: 0; }
        .close-modal { background: none; border: none; font-size: 2rem; color: var(--text-muted); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: var(--danger); }
        
        /* Tabs Styling */
        .tabs-container { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 10px; }
        .tab-btn { background: none; border: none; font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 500; color: var(--text-muted); padding: 8px 16px; cursor: pointer; border-radius: var(--radius-md); transition: var(--transition); }
        .tab-btn:hover { background: rgba(0,0,0,0.03); color: var(--text-main); }
        .tab-btn.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
        
        /* Bar for bulk actions */
        .actions-bar { display: flex; align-items: center; gap: 15px; background: rgba(0,0,0,0.02); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px; }
        .actions-bar label { margin: 0; font-weight: 500; cursor: pointer; }
        .checkbox-cell { width: 50px; text-align: center; }
        input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('insideuser.dashboard') }}" class="nav-link">
                    <span class="nav-icon">📊</span> Overview
                </a>
                <a href="{{ route('insideuser.profile.show') }}" class="nav-link">
                    <span class="nav-icon">👤</span> Profile
                </a>
                <a href="{{ route('insideuser.events.dashboard') }}" class="nav-link active">
                    <span class="nav-icon">🎉</span> My Events
                </a>
                <a href="{{ route('insideuser.connection.requests') }}" class="nav-link">
                    <span class="nav-icon">🤝</span> Connection Requests
                </a>
                <a href="{{ route('insideuser.connected.parents') }}" class="nav-link">
                    <span class="nav-icon">👨‍👩‍👧</span> Connected Parents
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            
            <header class="top-header" style="margin-bottom: 30px;">
                <div class="header-left">
                    <a href="{{ route('insideuser.events.show', $event->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.95rem; display: inline-block; margin-bottom: 15px;">&larr; Back to {{ $event->event_name }}</a>
                    <h1 class="fade-in">Registration <span class="highlight">Approvals</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Review and manage pending registrations requiring your approval.</p>
                </div>
                <div class="header-right fade-in" style="animation-delay: 0.1s;">
                    <a href="{{ route('insideuser.events.registrations', $event->id) }}" class="btn btn-secondary">View All Registrations</a>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert-info-box" style="background: var(--success-light); border-left-color: var(--success);">
                        <strong style="color: var(--success);">Success!</strong> <span style="color: var(--success);">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert-info-box" style="background: var(--warning-light); border-left-color: var(--warning); color: #B45309;">
                        <strong>Warning!</strong> <span>{{ session('warning') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert-info-box">
                        <strong>Info:</strong> <span>{{ session('info') }}</span>
                    </div>
                @endif

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Pending Approval</h3>
                        <div class="value" style="color: var(--warning);">{{ $statistics['pending_count'] }}</div>
                    </div>
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Approved</h3>
                        <div class="value" style="color: var(--success);">{{ $statistics['approved_count'] }}</div>
                    </div>
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Total Registrations</h3>
                        <div class="value" style="color: var(--primary);">{{ $statistics['total_registrations'] }}</div>
                    </div>
                </div>

                <!-- Tabs & Content Container -->
                <div class="glass-card">
                    <div class="tabs-container">
                        <button class="tab-btn active" onclick="showTab('pending')">Pending Approvals ({{ $statistics['pending_count'] }})</button>
                        <button class="tab-btn" onclick="showTab('approved')">Approved Logs ({{ $statistics['approved_count'] }})</button>
                    </div>

                    <!-- Pending Section -->
                    <div id="pending-section">
                        @if($pendingRegistrations->count() > 0)
                            <form id="bulk-approve-form" action="{{ route('insideuser.events.approvals.bulk-approve', $event->id) }}" method="POST">
                                @csrf
                                <div class="actions-bar">
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                                    <label for="select-all">Select All</label>
                                    <div style="width: 1px; height: 20px; background: rgba(0,0,0,0.1); margin: 0 5px;"></div>
                                    <span id="selected-count" style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500; min-width: 80px;">0 selected</span>
                                    
                                    <div style="flex-grow: 1; text-align: right;">
                                        <button type="button" class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);" onclick="showRejectModal()">Reject Selected</button>
                                        <button type="submit" class="btn btn-success" style="margin-left: 10px;">Approve Selected</button>
                                    </div>
                                </div>
                            </form>

                            <form id="bulk-reject-form" action="{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="registration_ids" id="reject-ids">
                            </form>

                            <div class="table-responsive">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-cell"></th>
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
                                                <input type="checkbox" name="registration_ids[]" value="{{ $reg->id }}" form="bulk-approve-form" class="reg-checkbox" onchange="updateSelectedCount()">
                                            </td>
                                            <td><strong>{{ $reg->fullname }}</strong></td>
                                            <td>{{ $reg->email }}</td>
                                            <td>{{ $reg->phone_number ?? '-' }}</td>
                                            <td><code style="font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px;">{{ $reg->qr_code }}</code></td>
                                            <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $reg->created_at->format('M d, Y g:i A') }}</td>
                                            <td style="display: flex; gap: 8px;">
                                                <form action="{{ route('insideuser.events.approvals.approve', $reg->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="badge badge-green" style="border: none; cursor: pointer;">Approve</button>
                                                </form>
                                                <button type="button" class="badge badge-red" style="border: none; cursor: pointer;" onclick="showSingleRejectModal({{ $reg->id }})">Reject</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 20px;">
                                {{ $pendingRegistrations->links() }}
                            </div>
                        @else
                            <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                                <div style="font-size: 3rem; margin-bottom: 20px;">✅</div>
                                <h4 style="font-size: 1.2rem; color: var(--success); margin-bottom: 10px;">All Caught Up!</h4>
                                <p style="color: var(--text-muted);">No pending registrations awaiting your approval.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Approved Section -->
                    <div id="approved-section" style="display: none;">
                        @if($approvedRegistrations->count() > 0)
                            <div class="table-responsive">
                                <table class="modern-table">
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
                                            <td><strong>{{ $reg->fullname }}</strong></td>
                                            <td>{{ $reg->email }}</td>
                                            <td>{{ $reg->phone_number ?? '-' }}</td>
                                            <td><code style="font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px;">{{ $reg->qr_code }}</code></td>
                                            <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $reg->creator_approved_at ? $reg->creator_approved_at->format('M d, Y g:i A') : 'N/A' }}</td>
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
                            </div>
                            <div style="margin-top: 20px;">
                                {{ $approvedRegistrations->links() }}
                            </div>
                        @else
                            <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                                <div style="font-size: 3rem; margin-bottom: 20px;">📂</div>
                                <h4 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 10px;">No approved logs yet.</h4>
                                <p style="color: var(--text-muted);">Approved registrations will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h2>Reject Registration</h2>
                <button class="close-modal" onclick="hideRejectModal()">×</button>
            </div>
            
            <form id="reject-form" method="POST">
                @csrf
                <input type="hidden" name="registration_ids" id="modal-reject-ids">
                <div class="form-group">
                    <label for="rejection_reason">Rejection Reason (Optional)</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" placeholder="Briefly explain why..."></textarea>
                </div>

                <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); gap: 15px;">
                    <button type="submit" class="btn" style="flex: 1; background: var(--danger); color: white;">Confirm Rejection</button>
                    <button type="button" class="btn btn-secondary" onclick="hideRejectModal()" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.reg-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                // Add to the bulk-reject form as well so it's synced if they click Reject
                const rejectInput = document.getElementById('reject-ids');
                if (checkbox.checked) {
                    // It will be grabbed dynamically anyways
                }
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.reg-checkbox:checked').length;
            document.getElementById('selected-count').textContent = checked + (checked === 1 ? ' selected' : ' selected');
        }

        function showTab(tabName) {
            const pendingSection = document.getElementById('pending-section');
            const approvedSection = document.getElementById('approved-section');
            const tabs = document.querySelectorAll('.tab-btn');

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
            document.getElementById('reject-form').action = "{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}";
            document.getElementById('reject-modal').classList.add('active');
        }

        function showSingleRejectModal(registrationId) {
            document.getElementById('modal-reject-ids').value = registrationId;
            document.getElementById('reject-form').action = "{{ route('insideuser.events.approvals.bulk-reject', $event->id) }}";
            document.getElementById('reject-modal').classList.add('active');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reject-modal');
            if (event.target === modal) {
                hideRejectModal();
            }
        }
        
        // Close modal on escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') hideRejectModal();
        });
    </script>
</body>
</html>
