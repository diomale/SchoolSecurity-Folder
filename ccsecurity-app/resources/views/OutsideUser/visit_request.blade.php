<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Visit - School Security</title>
    @vite(['resources/css/OutsideUser/outsideuser_style_visit_request.css'])
</head>
<body>
    <div class="visit-request-container">
        <div class="glass-card">
            <div class="form-header">
                <h1>Request a Visit</h1>
                <p>Schedule your upcoming visit to the campus</p>
            </div>
            
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('outsideuser.visit.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="visit_date">Visit Date <span style="color:red;">*</span></label>
                    <input type="date" id="visit_date" name="visit_date" class="form-control"
                        min="{{ date('Y-m-d') }}" 
                        value="{{ old('visit_date') }}" 
                        required>
                </div>

                <div class="form-group">
                    <label for="visit_time">Visit Time <span style="color:red;">*</span></label>
                    <input type="time" id="visit_time" name="visit_time" class="form-control"
                        value="{{ old('visit_time') }}" 
                        required>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose of Visit <span style="color:red;">*</span></label>
                    <textarea id="purpose" name="purpose" rows="4" class="form-control"
                        placeholder="e.g., Parent-teacher meeting, Pick up student, Business meeting..." 
                        required>{{ old('purpose') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="person_to_meet">Person to Meet <span style="color:red;">*</span></label>
                    <input type="text" id="person_to_meet" name="person_to_meet" class="form-control"
                        value="{{ old('person_to_meet') }}" 
                        placeholder="e.g., John Doe, Principal, Teacher Name..." 
                        required>
                </div>

                <div class="qr-status-info">
                    <div class="status-row">
                        <p>QR Code Status</p>
                        @if(auth('outsideuser')->user() && auth('outsideuser')->user()->qr_status === 'active')
                            <span class="status-badge status-active">● ACTIVE</span>
                        @else
                            <span class="status-badge status-inactive">● INACTIVE</span>
                        @endif
                    </div>
                    <em>After admin approval, your QR code will be activated for the visit.</em>
                </div>

                <div class="form-actions">
                    <a href="{{ route('outsider.dashboard') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
