<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: #000000;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .verification-code {
            background: #f8f9fa;
            border: 2px dashed #000000;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .verification-code .code {
            font-size: 36px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 8px;
        }
        .info-text {
            color: #666666;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
            font-size: 13px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Columban College Security System</h1>
        </div>
        <div class="content">
            <h2>New Device Detected</h2>
            <p class="info-text">
                We detected a login attempt from a new device or browser.
                To verify your identity, please use the following verification code:
            </p>
            
            <div class="verification-code">
                <div class="code">{{ $verificationCode }}</div>
            </div>
            
            <p class="info-text">
                This code will expire in <strong>15 minutes</strong>.
            </p>
            
            @if($deviceInfo)
            <p class="info-text">
                <strong>Device Info:</strong> {{ $deviceInfo }}
            </p>
            @endif
            
            <div class="warning">
                <strong>Security Notice:</strong> If you did not attempt to log in from a new device, 
                please ignore this email and consider changing your password.
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Columban College Security System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
