<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Requests - Admin</title>
</head>
<body>
    <div>
        <h1> Visit Requests Management</h1>
        
        <p><a href="{{ route('admin.dashboard') }}">← Back to Dashboard</a></p>

        @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
        @endif

        @if($visitRequests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Visitor Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Visit Date</th>
                    <th>Visit Time</th>
                    <th>Purpose</th>
                    <th>Person to Meet</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitRequests as $request)
                <tr>
                    <td>#{{ $request->id }}</td>
                    <td>{{ $request->outsideUser->fullname ?? 'N/A' }}</td>
                    <td>{{ $request->outsideUser->email ?? 'N/A' }}</td>
                    <td>{{ $request->outsideUser->phone_number ?? 'N/A' }}</td>
                    <td>{{ $request->visit_date->format('M d, Y') }}</td>
                    <td>{{ $request->visit_time->format('h:i A') }}</td>
                    <td>{{ $request->purpose }}</td>
                    <td>{{ $request->person_to_meet }}</td>
                    <td>
                        @if($request->status === 'approved')
                            <span> Approved</span>
                        @elseif($request->status === 'rejected')
                            <span> Rejected</span>
                        @else
                            <span> Pending</span>
                        @endif
                    </td>
                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <form action="{{ route('admin.visit.approve', $request->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Approve</button>
                            </form>
                            
                            <form action="{{ route('admin.visit.reject', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reject this visit request?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Reject</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
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
        @endif
    </div>
</body>
</html>
