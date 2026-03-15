<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registrations</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-blue { background: #d1ecf1; color: #0c5460; }
        .nav-link { color: #007bff; text-decoration: none; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; margin: auto; padding: 30px; border-radius: 8px; width: 100%; max-width: 450px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); animation: modalSlideIn 0.3s ease-out; }
        @keyframes modalSlideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .modal-header h2 { margin: 0; font-size: 20px; color: #333; }
        .close-modal { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; border: none; background: none; padding: 0; width: 30px; height: 30px; }
        .close-modal:hover { color: #000; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #007bff; }
        .modal-footer { display: flex; gap: 10px; margin-top: 20px; }
        .modal-footer .btn { flex: 1; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <a href="{{ route('insideuser.events.show', $event->id) }}" class="nav-link" style="font-size: 12px;">← Back to Event</a>
                    <h1 style="margin: 5px 0 0 0; font-size: 24px;">Event Registrations</h1>
                    <p style="margin: 5px 0 0 0; color: #666;">{{ $event->event_name }}</p>
                </div>
                <div>
                    <button onclick="openModal()" class="btn btn-primary">+ Register Walk-in</button>
                    <a href="{{ route('insideuser.events.exportRegistrations', $event->id) }}" class="btn btn-success"> Export CSV</a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div style="font-size: 12px; color: #666;">Total Registered</div>
                <div style="font-size: 28px; font-weight: bold;">{{ $registrations->total() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 12px; color: #666;">Checked In</div>
                <div style="font-size: 28px; font-weight: bold; color: #007bff;">{{ $registrations->where('status', 'checked_in')->count() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 12px; color: #666;">Available Slots</div>
                <div style="font-size: 28px; font-weight: bold; color: #28a745;">{{ $event->alien_user_limit - $registrations->total() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 12px; color: #666;">Capacity</div>
                <div style="font-size: 28px; font-weight: bold; color: #6f42c1;">{{ $event->alien_user_limit }}</div>
            </div>
        </div>

        <div class="card">
            @if($registrations->count() > 0)
            <table>
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
                        <td>{{ $registrations->firstItem() + $index }}</td>
                        <td>{{ $reg->fullname }}</td>
                        <td>{{ $reg->email }}</td>
                        <td>{{ $reg->phone_number ?? '-' }}</td>
                        <td><code style="font-size: 11px; background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $reg->qr_code }}</code></td>
                        <td>
                            @if($reg->needs_creator_approval && !$reg->creator_approved_at)
                                <span class="badge" style="background: #fff3cd; color: #856404;">Pending Approval</span>
                            @else
                                <span class="badge" style="background: #d4edda; color: #155724;">Approved</span>
                                @if($reg->creator_approved_at)
                                    <div style="font-size: 10px; color: #666; margin-top: 3px;">
                                        {{ $reg->creator_approved_at->format('M d, g:i A') }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-green">{{ ucfirst($reg->status) }}</span>
                        </td>
                        <td>{{ $reg->created_at->format('M d, Y g:i A') }}</td>
                        <td>
                            @if($reg->creator_approved_at)
                                <a href="{{ route('insideuser.events.downloadQR', $reg->id) }}" target="_blank" class="nav-link" title="Download QR">⬇</a>
                                <a href="{{ route('insideuser.events.resendQR', $reg->id) }}" class="nav-link" title="Resend Email" onclick="return confirm('Resend QR code to {{ $reg->email }}?')">✉</a>
                            @else
                                <span style="color: #999; font-size: 12px;">Awaiting approval</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $registrations->links() }}</div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <p style="color: #666;">No registrations yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Walk-in Registration Modal -->
    <div id="walkinModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Register Walk-in Participant</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>
            
            <form action="{{ route('insideuser.events.registerWalkin', $event->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="first_name">First Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter first name">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter last name">
                </div>

                <div class="form-group">
                    <label for="email">Email <span style="color: #dc3545;">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="Enter email address">
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="Enter phone number (optional)">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Participant</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('walkinModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('walkinModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('walkinModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
