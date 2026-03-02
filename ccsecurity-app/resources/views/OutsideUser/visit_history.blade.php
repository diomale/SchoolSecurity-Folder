<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit History - School Security</title>
</head>
<body>
    <div>
        <h1>My Visit History</h1>
        
        <p><a href="{{ route('outsider.dashboard') }}">← Back to Dashboard</a></p>

        @if($visitRequests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Visit Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Person to Meet</th>
                    <th>Status</th>
                    <th>Admin Remarks</th>
                    <th>Requested On</th>
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
                            <span>✓ Approved</span>
                        @elseif($request->status === 'rejected')
                            <span>✗ Rejected</span>
                        @else
                            <span>⏳ Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($request->admin_remarks)
                            {{ $request->admin_remarks }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($visitRequests->hasPages())
        <div>
            {{ $visitRequests->links() }}
        </div>
        @endif

        @else
        <p>No visit requests found.</p>
        <a href="{{ route('outsideuser.visit.request') }}">Request a Visit</a>
        @endif
    </div>
</body>
</html>
