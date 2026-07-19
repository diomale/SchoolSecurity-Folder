<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css', 'resources/css/InsideUser/insideuser_style_user_profile.css'])
    <style>
        .custom-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .custom-modal.active { display: flex; }
        .custom-modal-content { background: white; padding: 30px; border-radius: var(--radius-xl); width: 100%; max-width: 450px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; text-align: center; }
        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .qr-code-wrapper { cursor: pointer; transition: transform 0.2s ease; }
        .qr-code-wrapper:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        @include('InsideUser.partials.sidebar', ['activePage' => 'profile'])

        <!-- Main Content Area -->
        <main class="main-content">
            
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">My <span class="highlight">Profile</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage your personal details and view your Entry/Exit QR Code.</p>
                </div>
            </header>

            <div class="profile-container fade-in" style="animation-delay: 0.2s;">
                <!-- Profile Information -->
                <div class="glass-card profile-card">
                    @php
                        $user = auth('insideuser')->user();
                    @endphp
                    <h2 class="profile-name">{{ $user->fullname }}</h2>
                    <p class="profile-email">{{ $user->email }}</p>

                    <div class="profile-details">
                        <div class="detail-row">
                            <span class="detail-label">Account Type</span>
                            <span class="detail-value" style="color: var(--primary);">Inside User</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Full Name</span>
                            <span class="detail-value">{{ $user->fullname }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-value" style="color: var(--success);">Active</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="glass-card qr-card">
                    <h3 class="section-title" style="margin-bottom: 20px; border:none; justify-content:center;">Digital Pass</h3>
                    
                    <div class="qr-code-wrapper" onclick="openQrModal()" title="Click to enlarge">
                        {!! QrCode::size(220)->margin(1)->generate($user->qr_value) !!}
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        @if($user->qr_status === 'active')
                            <div class="qr-status-badge status-active">
                                <span class="dot"></span> QR ACTIVE
                            </div>
                        @else
                            <div class="qr-status-badge status-inactive">
                                <span class="dot"></span> QR INACTIVE
                            </div>
                        @endif
                    </div>

                    <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 250px; line-height: 1.5; margin:0 auto;">
                        Present this QR code to the security guard at the entrance to log your entry and exit.
                    </p>
                </div>
            </div>

        </main>
    </div>

    <!-- QR Enlarge Modal -->
    <div id="qrModal" class="custom-modal" onclick="closeQrModal()">
        <div class="custom-modal-content" onclick="event.stopPropagation()">
            <h2 style="margin-bottom: 20px; color: var(--text-main); font-size: 1.5rem;">Digital Pass</h2>
            <div style="background: white; padding: 20px; border: 2px dashed rgba(0,0,0,0.1); border-radius: var(--radius-md); display: inline-block; margin-bottom: 25px;">
                {!! QrCode::size(300)->margin(1)->generate($user->qr_value) !!}
            </div>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 25px;">
                {{ $user->fullname }}
            </p>
            <button onclick="closeQrModal()" class="btn btn-primary" style="width: 100%; justify-content: center;">Close</button>
        </div>
    </div>

    <script>
        function openQrModal() {
            document.getElementById('qrModal').classList.add('active');
        }

        function closeQrModal() {
            document.getElementById('qrModal').classList.remove('active');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQrModal();
            }
        });
    </script>
</body>
</html>
