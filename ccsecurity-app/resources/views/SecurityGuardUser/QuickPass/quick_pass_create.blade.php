<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quick Pass - Security Guard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }
        .form-group label .required {
            color: #dc3545;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        .error-messages {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-primary {
            background: #28a745;
            color: white;
            width: 100%;
        }
        .btn-primary:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-top: 10px;
            width: 100%;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-box h4 {
            margin: 0 0 10px 0;
            color: #004085;
            font-size: 14px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #004085;
            font-size: 13px;
        }
        .info-box ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Link -->
        <a href="{{ route('security.quick-pass.list') }}" class="back-link">← Back to Quick Pass List</a>

        <!-- Header -->
        <div class="header">
            <h1> Create Quick Pass</h1>
            <p>Generate a temporary QR code for same-day visitor access</p>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <h4> Quick Pass Benefits:</h4>
            <ul>
                <li> No email or phone required</li>
                <li> QR code generated instantly</li>
                <li> Valid until 11:59 PM today</li>
                <li> Perfect for visitors in vehicles</li>
            </ul>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
        <div class="error-messages">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('security.quick-pass.store') }}" method="POST">
            @csrf

            <!-- Visitor Name -->
            <div class="form-group">
                <label for="visitor_name">
                    Visitor Name
                    <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="visitor_name"
                    name="visitor_name"
                    value="{{ old('visitor_name') }}"
                    placeholder="e.g., John Doe"
                    required
                    autofocus
                >
                <small>Full name of the visitor</small>
            </div>

            <!-- Vehicle Plate -->
            <div class="form-group">
                <label for="vehicle_plate">
                    Vehicle Plate Number
                </label>
                <input
                    type="text"
                    id="vehicle_plate"
                    name="vehicle_plate"
                    value="{{ old('vehicle_plate') }}"
                    placeholder="e.g., ABC-123 (optional)"
                    style="text-transform: uppercase;"
                >
                <small>Leave blank if visitor is on foot</small>
            </div>

            <!-- Purpose -->
            <div class="form-group">
                <label for="purpose">
                    Purpose of Visit
                    <span class="required">*</span>
                </label>
                <select id="purpose" name="purpose" required>
                    <option value="">Select Purpose</option>
                    <option value="Delivery" {{ old('purpose') === 'Delivery' ? 'selected' : '' }}> Delivery</option>
                    <option value="Meeting" {{ old('purpose') === 'Meeting' ? 'selected' : '' }}> Meeting</option>
                    <option value="Parent" {{ old('purpose') === 'Parent' ? 'selected' : '' }}> Parent/Guardian</option>
                    <option value="Contractor" {{ old('purpose') === 'Contractor' ? 'selected' : '' }}> Contractor</option>
                    <option value="Other" {{ old('purpose') === 'Other' ? 'selected' : '' }}> Other</option>
                </select>
            </div>

            <!-- Custom Expiry (For Testing/Special cases) -->
            <div class="form-group">
                <label for="expiry_time">
                    Custom Expiration (Today)
                </label>
                <input
                    type="time"
                    id="expiry_time"
                    name="expiry_time"
                    value="{{ old('expiry_time') }}"
                >
                <small>Leave blank for default (11:59 PM today)</small>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">
                 Generate Quick Pass
            </button>

            <!-- Cancel Button -->
            <a href="{{ route('security.quick-pass.list') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>

    <script>
        // Auto-uppercase vehicle plate
        document.getElementById('vehicle_plate').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
