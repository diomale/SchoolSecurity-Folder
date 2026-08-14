<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registrations - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css', 'resources/css/InsideUser/insideuser_style_events.css'])
    <style>
        .custom-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .custom-modal.active { display: flex; }
        .custom-modal-content { background: white; padding: 30px; border-radius: var(--radius-xl); width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .custom-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 15px; }
        .custom-modal-header h2 { font-size: 1.4rem; color: var(--text-main); margin: 0; }
        .close-modal { background: none; border: none; font-size: 2rem; color: var(--text-muted); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: var(--danger); }
        .table-action-icons a { font-size: 1.2rem; margin-right: 10px; text-decoration: none; transition: var(--transition); display:inline-block; }
        .table-action-icons a:hover { transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('InsideUser.partials.sidebar', ['activePage' => 'events'])

        <!-- Main Content Area -->
        <main class="main-content">
            
            <header class="top-header" style="margin-bottom: 30px;">
                <div class="header-left">
                    <a href="{{ route('insideuser.events.show', $event->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.95rem; display: inline-block; margin-bottom: 15px;">&larr; Back to {{ $event->event_name }}</a>
                    <h1 class="fade-in">Event <span class="highlight">Registrations</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage and export registrations for your event.</p>
                </div>
                <div class="header-right fade-in" style="animation-delay: 0.1s; display: flex; gap: 10px;">
                    <a href="{{ route('insideuser.events.pending-approvals', $event->id) }}" class="btn btn-secondary" style="border: 1px solid rgba(255,255,255,0.12); color: var(--text-main);">Approvals ({{ $registrations->where('needs_creator_approval', true)->whereNull('creator_approved_at')->count() }})</a>
                    <button onclick="openModal()" class="btn btn-primary">+ Register Walk-in</button>
                    <a href="{{ route('insideuser.events.exportRegistrations', $event->id) }}" class="btn btn-success" style="background: var(--success);">Export CSV</a>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                @if(session('success'))
                    <div class="alert-info-box" style="background: var(--success-light); border-left-color: var(--success);">
                        <strong style="color: var(--success);">Success!</strong> <span style="color: var(--success);">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert-info-box" style="background: var(--danger-light); border-left-color: var(--danger);">
                        <strong style="color: var(--danger);">Error!</strong> <span style="color: var(--danger);">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Total Registered</h3>
                        <div class="value" style="color: var(--primary);">{{ $registrations->total() }}</div>
                    </div>
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Checked In</h3>
                        <div class="value" style="color: var(--info);">{{ $registrations->where('status', 'checked_in')->count() }}</div>
                    </div>
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Available Slots</h3>
                        <div class="value" style="color: var(--success);">{{ $event->alien_user_limit - $registrations->total() }}</div>
                    </div>
                    <div class="stat-card">
                        <h3 style="margin-bottom: 5px;">Capacity</h3>
                        <div class="value" style="color: var(--purple);">{{ $event->alien_user_limit }}</div>
                    </div>
                </div>

                <!-- Registrations Table -->
                <div class="glass-card">
                    @if($registrations->count() > 0)
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>QR Code</th>
                                    <th>Approval Status</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrations as $index => $reg)
                                <tr>
                                    <td class="text-muted">{{ $registrations->firstItem() + $index }}</td>
                                    <td><strong>{{ $reg->fullname }}</strong></td>
                                    <td>{{ $reg->email }}</td>
                                    <td>{{ $reg->phone_number ?? '-' }}</td>
                                    <td><code style="font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 4px;">{{ $reg->qr_code }}</code></td>
                                    <td>
                                        @if($reg->needs_creator_approval && !$reg->creator_approved_at)
                                            <span class="badge badge-yellow">Pending</span>
                                        @else
                                            <span class="badge badge-green">Approved</span>
                                            @if($reg->creator_approved_at)
                                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                                    {{ $reg->creator_approved_at->format('M d, g:i A') }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-green">{{ ucfirst($reg->status) }}</span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $reg->created_at->format('M d, Y g:i A') }}</td>
                                    <td class="table-action-icons" style="white-space: nowrap;">
                                        @if($reg->needs_creator_approval && !$reg->creator_approved_at)
                                            <form action="{{ route('insideuser.events.approvals.approve', $reg->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Approve {{ $reg->fullname }}? QR code will be emailed.')" title="Approve" style="background:none;border:none;cursor:pointer;color:var(--success);font-size:1.2rem;display:inline-block;margin-right:8px;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>
                                            </form>
                                            <form action="{{ route('insideuser.events.approvals.reject', $reg->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Reject {{ $reg->fullname }}?')" title="Reject" style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:1.2rem;display:inline-block;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                                            </form>
                                        @elseif($reg->creator_approved_at)
                                            <a href="{{ route('insideuser.events.downloadQR', $reg->id) }}" target="_blank" title="Download QR"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
                                            <form action="{{ route('insideuser.events.resendQR', $reg->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Resend QR code to {{ $reg->email }}?')">
                                                @csrf
                                                <button type="submit" title="Resend Email" style="background:none;border:none;cursor:pointer;color:var(--text-main);padding:0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></button>
                                            </form>
                                        @else
                                            <span style="color: var(--text-light); font-size: 0.8rem;">Awaiting</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 20px;">{{ $registrations->links() }}</div>
                    @else
                    <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 20px;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg></div>
                        <h4 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 10px;">No registrations yet..</h4>
                        <p style="color: var(--text-muted);">As participants register, they will appear here.</p>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Walk-in Registration Modal -->
    <div id="walkinModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h2>Register Walk-in Participant</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <form action="{{ route('insideuser.events.registerWalkin', $event->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="first_name">First Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter first name">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter last name">
                </div>

                <div class="form-group">
                    <label for="email">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="Enter email address">
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="Enter phone number (optional)">
                </div>

                <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); gap: 15px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Register Participant</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('walkinModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('walkinModal').classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(e) {
            let modal = document.getElementById('walkinModal');
            if (e.target === modal) {
                closeModal();
            }
        };

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
