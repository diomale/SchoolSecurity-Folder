<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View QR Code - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_walkin.css'])
    <style>
        body { 
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding-top: 20px;">
        @php
            $from = request('from');
            $backRoute = route('security.walkin.list');
            $backLabel = 'Walk-in List';
            if($from === 'qr-status' || $type === 'inside') {
                $backRoute = route('security.qr.status.management');
                $backLabel = 'QR Status Management';
            }
        @endphp
        
        <div class="no-print">
            <a href="{{ $backRoute }}" class="standalone-back">← Back to {{ $backLabel }}</a>
        </div>

        <div class="qr-view-container">
            @if($type === 'inside')
                <h2>{{ $user->role === 'student' ? 'Student' : 'Staff' }} QR Code</h2>
            @else
                <h2>Visitor QR Code</h2>
            @endif
            <p style="color: #6b7280; margin-bottom: 20px; font-size: 0.95rem;">Please show this to the visitor to scan or take a photo.</p>
            
            <div class="qr-code-wrapper">
                @if($user->qr_value)
                    {!! QrCode::size(300)->margin(1)->generate($user->qr_value) !!}
                @else
                    <div style="color: #dc2626; font-weight: bold; padding: 40px; border: 2px dashed #fca5a5; border-radius: 8px;">QR Code value missing!</div>
                @endif
            </div>

            <div class="qr-info-box">
                <p><strong>Name</strong> <span>{{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</span></p>
                <p><strong>Email</strong> <span>{{ $user->email }}</span></p>
                @if(isset($user->phone_number))
                <p><strong>Phone</strong> <span>{{ $user->phone_number }}</span></p>
                @endif
            </div>

            <div class="no-print">
                <button onclick="window.print()" class="btn-print">🖨️ Print QR Code</button>
            </div>
        </div>
    </div>
</body>
</html>