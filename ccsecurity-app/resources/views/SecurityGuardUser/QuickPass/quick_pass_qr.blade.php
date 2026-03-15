<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Pass QR - {{ $quickPass->visitor_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 450px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 22px;
        }
        .expiry-banner {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #000;
            padding: 12px 20px;
            border-radius: 6px;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .expiry-banner .icon {
            font-size: 18px;
            margin-right: 8px;
        }
        .qr-container {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px dashed #dee2e6;
        }
        .qr-container img {
            max-width: 200px;
            height: auto;
        }
        .visitor-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .visitor-info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .visitor-info-row:last-child {
            border-bottom: none;
        }
        .visitor-info-row label {
            font-weight: 600;
            color: #666;
            font-size: 13px;
        }
        .visitor-info-row span {
            color: #333;
            font-size: 14px;
        }
        .purpose-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-expired {
            background: #f8d7da;
            color: #721c24;
        }
        .status-used {
            background: #e2e3e5;
            color: #383d41;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .instructions {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
        }
        .instructions h4 {
            margin: 0 0 10px 0;
            color: #004085;
            font-size: 14px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #004085;
            font-size: 13px;
        }
        .instructions ol li {
            margin-bottom: 5px;
        }
        .qr-code-text {
            font-family: monospace;
            background: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 12px;
            word-break: break-all;
            text-align: center;
            margin-top: 10px;
        }
        @media print {
            .btn-group, .back-link, .instructions {
                display: none !important;
            }
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Link -->
        <a href="{{ route('security.quick-pass.list') }}" class="back-link" style="display: block; margin-bottom: 15px; color: #007bff; text-decoration: none;">
            ← Back to List
        </a>

        <!-- Header -->
        <div class="header">
            <h1> Quick Pass</h1>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Temporary Visitor Access</p>
        </div>

        <!-- Expiry Banner -->
        <div class="expiry-banner">
            <span class="icon"></span>
            VALID UNTIL {{ $quickPass->expires_at->format('h:i A') }} TODAY
            <span class="icon"></span>
        </div>

        <!-- QR Code -->
        <div class="qr-container" id="qr-code-area">
            {!! QrCode::size(200)->generate($quickPass->qr_value) !!}
        </div>

        <!-- Visitor Info -->
        <div class="visitor-info">
            <div class="visitor-info-row">
                <label>Visitor Name:</label>
                <span><strong>{{ $quickPass->visitor_name }}</strong></span>
            </div>
            <div class="visitor-info-row">
                <label>Vehicle Plate:</label>
                <span>{{ $quickPass->vehicle_plate ?? '—' }}</span>
            </div>
            <div class="visitor-info-row">
                <label>Purpose:</label>
                <span>
                    <span class="purpose-badge" style="background: {{ $quickPass->purpose_color }};">
                        {{ $quickPass->purpose }}
                    </span>
                </span>
            </div>
            <div class="visitor-info-row">
                <label>Status:</label>
                <span>
                    @if($quickPass->isExpired())
                        <span class="status-badge status-expired">Expired</span>
                    @elseif($quickPass->status === 'used')
                        <span class="status-badge status-used">Used</span>
                    @else
                        <span class="status-badge status-active">Active</span>
                    @endif
                </span>
            </div>
            <div class="visitor-info-row">
                <label>Generated:</label>
                <span>{{ $quickPass->created_at?->format('h:i A') ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-group">
            <button type="button" onclick="printQR()" class="btn btn-primary">
                 Print
            </button>
            <button type="button" onclick="takeScreenshot()" class="btn btn-success">
                 Screenshot
            </button>
            <a href="{{ route('security.quick-pass.list') }}" class="btn btn-secondary">
                 Done
            </a>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h4> How to use:</h4>
            <ol>
                <li><strong>Print:</strong> Print this QR code on paper for the visitor</li>
                <li><strong>Screenshot:</strong> Take a screenshot and show to visitor</li>
                <li>Visitor shows QR at gate for scanning</li>
                <li>QR code expires at 11:59 PM today</li>
            </ol>
        </div>
    </div>

    <script>
        function printQR() {
            window.print();
        }

        function takeScreenshot() {
            // Simple method: select the QR area for screenshot
            alert(' To capture:\n\nWindows: Press Win + Shift + S\nMac: Press Cmd + Shift + 4\n\nThen select the QR code area.');
        }
    </script>
</body>
</html>
