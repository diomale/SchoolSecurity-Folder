<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard - School Security</title>
</head>
<body>
    <div>
        <h1>Welcome, {{ auth('outsideuser')->user()->fullname }}</h1>
        
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

        <!-- Account Status -->
        <div>
            <h2>Account Status</h2>
            <p><strong>Email:</strong> {{ auth('outsideuser')->user()->email }}</p>
            <p><strong>Phone:</strong> {{ auth('outsideuser')->user()->phone_number }}</p>
            <p><strong>QR Code:</strong> {{ auth('outsideuser')->user()->qr_value }}</p>
            <p><strong>QR Status:</strong> 
                @if(auth('outsideuser')->user()->qr_status === 'active')
                    <span>Active ✓</span>
                @else
                    <span>Inactive ✗</span>
                @endif
            </p>
            <p><strong>Account Status:</strong> 
                @if(auth('outsideuser')->user()->status == 1)
                    <span>Approved ✓</span>
                @elseif(auth('outsideuser')->user()->status == 2)
                    <span>Rejected ✗</span>
                @else
                    <span>Pending ⏳</span>
                @endif
            </p>
        </div>

        <!-- Quick Actions -->
        <div>
            <h2>Quick Actions</h2>
            
            @if(auth('outsideuser')->user()->status == 1)
                <div>
                    <h3>Request a Visit</h3>
                    <p>Submit a visit request to activate your QR code</p>
                    <a href="{{ route('outsideuser.visit.request') }}">Request Visit</a>
                </div>

                <div>
                    <h3>My QR Code Pass</h3>
                    <p>Present this QR code to the guard at the entrance.</p>
                    <div style="margin-top: 15px; margin-bottom: 20px; padding: 20px; background: white; border: 2px solid #333; border-radius: 12px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
                        @if(auth('outsideuser')->user()->qr_value)
                            <div style="background: white; padding: 10px; border-radius: 4px;">
                                {!! QrCode::size(250)->margin(1)->generate(auth('outsideuser')->user()->qr_value) !!}
                            </div>
                            <div style="margin-top: 15px;">
                                <span style="font-family: monospace; font-size: 1.2rem; font-weight: bold; background: #f0f0f0; padding: 5px 10px; border-radius: 4px;">
                                    {{ auth('outsideuser')->user()->qr_value }}
                                </span>
                            </div>
                            <p style="margin-top: 10px; font-weight: bold;">
                                Status: 
                                @if(auth('outsideuser')->user()->qr_status === 'active')
                                    <span style="color: #28a745;">● ACTIVE (You can visit)</span>
                                @else
                                    <span style="color: #dc3545;">● INACTIVE (Visit request needed)</span>
                                @endif
                            </p>
                        @else
                            <p>Loading your pass...</p>
                        @endif
                    </div>
                </div>
            @else
                <div>
                    <p>Your account is pending admin approval. Please wait for approval before requesting visits.</p>
                </div>
            @endif

            <div>
                <h3>Visit History</h3>
                <p>View your past and upcoming visit requests</p>
                <a href="{{ route('outsideuser.visit.history') }}">View Visit History</a>
            </div>
        </div>

        <!-- Recent Visit Requests -->
        <div>
            <h2>Recent Visit Requests</h2>
            @if($visitRequests->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Purpose</th>
                        <th>Person to Meet</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visitRequests as $request)
                    <tr>
                        <td>{{ $request->visit_date->format('M d, Y') }}</td>
                        <td>{{ $request->visit_time->format('h:i A') }}</td>
                        <td>{{ $request->purpose }}</td>
                        <td>{{ $request->person_to_meet }}</td>
                        <td>
                            @if($request->status === 'approved')
                                Approved ✓
                            @elseif($request->status === 'rejected')
                                Rejected ✗
                            @else
                                Pending ⏳
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>No visit requests yet.</p>
            @endif
        </div>

        <!-- Logout -->
        <div>
            <form method="POST" action="{{ route('outsideuser.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
