<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Visit - School Security</title>
</head>
<body>
    <div>
        <h1>Request a Visit</h1>
        
        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div>
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div>
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('outsideuser.visit.submit') }}" method="POST">
            @csrf

            <div>
                <label for="visit_date">Visit Date *</label>
                <input type="date" id="visit_date" name="visit_date" 
                    min="{{ date('Y-m-d') }}" 
                    value="{{ old('visit_date') }}" 
                    required>
            </div>

            <div>
                <label for="visit_time">Visit Time *</label>
                <input type="time" id="visit_time" name="visit_time" 
                    value="{{ old('visit_time') }}" 
                    required>
            </div>

            <div>
                <label for="purpose">Purpose of Visit *</label>
                <textarea id="purpose" name="purpose" rows="4" 
                    placeholder="e.g., Parent-teacher meeting, Pick up student, Business meeting..." 
                    required>{{ old('purpose') }}</textarea>
            </div>

            <div>
                <label for="person_to_meet">Person to Meet *</label>
                <input type="text" id="person_to_meet" name="person_to_meet" 
                    value="{{ old('person_to_meet') }}" 
                    placeholder="e.g., John Doe, Principal, Teacher Name..." 
                    required>
            </div>

            <div>
                <p><strong>Your QR Code:</strong> {{ auth('outsideuser')->user()->qr_value }}</p>
                <p><em>After admin approval, your QR code will be activated for the visit.</em></p>
            </div>

            <div>
                <button type="submit">Submit Request</button>
                <a href="{{ route('outsider.dashboard') }}">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
