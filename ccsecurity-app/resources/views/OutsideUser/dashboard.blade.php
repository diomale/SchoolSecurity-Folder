<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard - School Security</title>
</head>
<body>
    <div>
        <div>
            <h1>Welcome, {{ auth('outsideuser')->user()->fullname }}</h1>
            <a href="{{ route('outsideuser.profile.show') }}">👤 My Profile</a>
        </div>

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

        <div>
            <h2>Quick Actions</h2>

            @if(auth('outsideuser')->user()->status == 1)
                <div>
                    <h3>Request a Visit</h3>
                    <p>Submit a visit request to activate your QR code</p>
                    <a href="{{ route('outsideuser.visit.request') }}">Request Visit</a>
                </div>

                <div>
                    <h3>Visit History</h3>
                    <p>View your past and upcoming visit requests</p>
                    <a href="{{ route('outsideuser.visit.history') }}">View Visit History</a>
                </div>

            @else
                <div>
                    <p>Your account is pending admin approval. Please wait for approval before requesting visits.</p>
                </div>
            @endif
        </div>

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

        <div>
            <form method="POST" action="{{ route('outsideuser.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
