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
    @vite(['resources/css/SecurityGuardUser/securityguard_style_dashboard.css', 'resources/css/SecurityGuardUser/securityguard_style_quickpass.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        @include('SecurityGuardUser.partials.sidebar', ['activePage' => 'quick-pass'])

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
                    <button type="button" onclick="printQR()" class="btn-primary" style="flex: 1; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        Print
                    </button>
                    <button type="button" onclick="takeScreenshot()" class="btn-primary" style="background: var(--success); flex: 1; justify-content: center;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg> Screenshot</button>
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
