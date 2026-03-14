<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View QR Code - Security Guard</title>
</head>
<body style="font-family: sans-serif; text-align: center; padding-top: 50px;">
    <div>
        @php
            $from = request('from');
        @endphp
        @if($from === 'qr-status')
            <a href="{{ route('security.qr.status.management') }}" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 20px;">← Back to QR Status Management</a>
        @elseif($from === 'walkin')
            <a href="{{ route('security.walkin.list') }}" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 20px;">← Back to Walk-in List</a>
        @elseif($type === 'inside')
            <a href="{{ route('security.qr.status.management') }}" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 20px;">← Back to QR Status Management</a>
        @else
            <a href="{{ route('security.walkin.list') }}" style="text-decoration: none; color: #007bff; display: inline-block; margin-bottom: 20px;">← Back to Walk-in List</a>
        @endif

        <div style="border: 2px solid #ddd; display: inline-block; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: white;">
            @if($type === 'inside')
                <h2 style="margin-top: 0; color: #333;">{{ $user->role === 'student' ? 'Student' : 'Staff' }} QR Code</h2>
            @else
                <h2 style="margin-top: 0; color: #333;">Visitor QR Code</h2>
            @endif
            <p style="color: #666; margin-bottom: 20px;">Please show this to the visitor to scan or take a photo.</p>
            
            <div style="margin-bottom: 25px; padding: 15px; background-color: white;">
                @if($user->qr_value)
                    {!! QrCode::size(300)->margin(1)->generate($user->qr_value) !!}
                    <div style="margin-top: 15px; font-weight: bold; font-family: monospace; font-size: 1.2rem; color: #555;">
                        {{ $user->qr_value }}
                    </div>
                @else
                    <div style="color: red; font-weight: bold;">QR Code value missing!</div>
                @endif
            </div>

            <div style="text-align: left; background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                <p><strong>Name:</strong> {{ $user->fullname ?? ($user->first_name . ' ' . $user->last_name) }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                @if(isset($user->phone_number))
                <p><strong>Phone:</strong> {{ $user->phone_number }}</p>
                @endif
            </div>
        </div>

        <div style="margin-top: 30px;" class="no-print">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print QR Code</button>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding-top: 0;
            }
            div[style*="border"] {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</body>
</html>