<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Pass QR - {{ $quickPass->visitor_name }}</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_dashboard.css', 'resources/css/SecurityGuardStyleFolder/securityguard_style_quickpass.css'])
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
                <a href="{{ route('security.dashboard') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
                </a>
                <a href="{{ route('security.quick-pass.list') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">🚗</span> Quick Pass
                </a>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('security.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <a href="{{ route('security.quick-pass.list') }}" class="back-link" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 20px;">
                &larr; Back to Quick Pass List
            </a>

            <div class="glass-card" style="max-width: 500px; margin: 0 auto;">
                <h2 style="text-align: center; margin-bottom: 5px; font-size: 1.8rem; font-weight: 800;">Quick Pass</h2>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 24px; font-weight: 500;">Temporary Visitor Access</p>

                <div class="expiry-banner fade-in">
                    VALID UNTIL {{ $quickPass->expires_at->format('h:i A') }} TODAY
                </div>

                <div class="qr-display-container fade-in" style="animation-delay: 0.1s;">
                    {!! QrCode::size(220)->margin(1)->generate($quickPass->qr_value) !!}
                </div>

                <div style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 24px;" class="fade-in" style="animation-delay: 0.2s;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px; margin-bottom: 10px;">
                        <span style="color: var(--text-muted); font-weight: 600; font-size: 0.95rem;">Visitor Name</span>
                        <strong style="color: var(--text-main);">{{ $quickPass->visitor_name }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px; margin-bottom: 10px;">
                        <span style="color: var(--text-muted); font-weight: 600; font-size: 0.95rem;">Vehicle Plate</span>
                        <span style="color: var(--text-main); font-family: monospace; font-weight: 600;">{{ $quickPass->vehicle_plate ?? '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px; margin-bottom: 10px;">
                        <span style="color: var(--text-muted); font-weight: 600; font-size: 0.95rem;">Purpose</span>
                        <span class="purpose-badge" style="background: {{ $quickPass->purpose_color }};">{{ $quickPass->purpose }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px; margin-bottom: 10px;">
                        <span style="color: var(--text-muted); font-weight: 600; font-size: 0.95rem;">Status</span>
                        <span>
                            @if($quickPass->isExpired())
                                <span class="badge badge-danger">Expired</span>
                            @elseif($quickPass->status === 'used')
                                <span class="badge badge-outline">Used</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted); font-weight: 600; font-size: 0.95rem;">Generated</span>
                        <span style="color: var(--text-main);">{{ $quickPass->created_at?->format('h:i A') ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="btn-group fade-in" style="display: flex; gap: 12px; animation-delay: 0.3s; margin-bottom: 24px;">
                    <button type="button" onclick="printQR()" class="btn-primary" style="flex: 1; justify-content: center;">🖨️ Print</button>
                    <button type="button" onclick="takeScreenshot()" class="btn-primary" style="background: var(--success); flex: 1; justify-content: center;">📷 Screenshot</button>
                </div>

                <div class="instructions fade-in" style="background: var(--bg-glass-strong); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border); animation-delay: 0.4s;">
                    <h4 style="margin-bottom: 10px; color: var(--primary-dark);">How to use:</h4>
                    <ol style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem;">
                        <li style="margin-bottom: 5px;"><strong>Print:</strong> Print this QR code on paper for the visitor</li>
                        <li style="margin-bottom: 5px;"><strong>Screenshot:</strong> Take a screenshot and show to visitor</li>
                        <li style="margin-bottom: 5px;">Visitor shows QR at gate for scanning</li>
                        <li>QR code expires at 11:59 PM today</li>
                    </ol>
                </div>
            </div>
        </main>
    </div>

    <script>
        function printQR() {
            window.print();
        }

        function takeScreenshot() {
            alert('To capture:\n\nWindows: Press Win + Shift + S\nMac: Press Cmd + Shift + 4\n\nThen select the QR code area.');
        }
    </script>
</body>
</html>
