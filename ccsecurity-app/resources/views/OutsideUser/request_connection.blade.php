<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Connection - School Security</title>
    <style>
        .search-results {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            display: none;
        }
        .search-result-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .search-result-item:hover {
            background-color: #f5f5f5f5;
        }
        .search-result-item.selected {
            background-color: #e3f2fd;
        }
        .connection-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }
        .status-approved {
            color: #4caf50;
            font-weight: bold;
        }
        .status-rejected {
            color: #f44336;
            font-weight: bold;
        }
        .status-accepted {
            color: #2196f3;
            font-weight: bold;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <h1>Request Child Connection</h1>
            <p>Connect with your child to track their entry and exit at school</p>
            <a href="{{ route('outsider.dashboard') }}">← Back to Dashboard</a> | 
            <a href="{{ route('outsideuser.connections.history') }}">View Connection History</a>
        </div>

        @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 15px 0;">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 15px 0;">
            <strong>Errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Connected Children Section -->
        @if($connectedChildren->count() > 0)
        <div class="connection-card">
            <h2>✓ Your Connected Children</h2>
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>QR Value</th>
                        <th>Relationship</th>
                        <th>Connected Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($connectedChildren as $child)
                    <tr>
                        <td>{{ $child->fullname }}</td>
                        <td>{{ $child->email }}</td>
                        <td>{{ $child->qr_value }}</td>
                        <td>{{ $child->pivot->relationship }}</td>
                        <td>{{ \Carbon\Carbon::parse($child->pivot->approved_at)->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Pending Requests Section -->
        @if($pendingRequests->count() > 0)
        <div class="connection-card">
            <h2>⏳ Pending Requests</h2>
            
            <div class="info-box">
                <strong>ℹ️ How it works:</strong>
                <ol style="margin: 10px 0 0 20px; padding: 0;">
                    <li>After you submit a request, the student will review it</li>
                    <li>Once the student accepts, you're immediately connected!</li>
                    <li>You can then view their entry/exit records in your dashboard</li>
                </ol>
            </div>
            
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Relationship</th>
                        <th>Student Approval</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $request)
                    <tr>
                        <td>{{ $request->insideUser->fullname }}</td>
                        <td>{{ $request->insideUser->email }}</td>
                        <td>{{ $request->relationship }}</td>
                        <td>
                            @if($request->inside_user_approval === 'accepted')
                                <span class="status-approved">✓ Accepted</span>
                            @elseif($request->inside_user_approval === 'rejected')
                                <span class="status-rejected">✗ Rejected</span>
                            @else
                                <span class="status-pending">⏳ Awaiting Student</span>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'approved')
                                <span class="status-approved">✓ Connected</span>
                            @elseif($request->status === 'rejected')
                                <span class="status-rejected">✗ Rejected</span>
                            @elseif($request->inside_user_approval === 'accepted')
                                <span class="status-approved">✓ Auto-approved</span>
                            @else
                                <span class="status-pending">⏳ Pending</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($request->inside_user_approval === 'pending')
                                <form action="{{ route('outsideuser.connections.cancel', $request->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                                </form>
                            @else
                                <span style="color: #999; font-size: 12px;">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Request Form Section -->
        <div class="connection-card">
            <h2>📝 Request New Connection</h2>
            <p>Search for your child by name or email to request a connection</p>
            
            <form action="{{ route('outsideuser.connections.submit') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 15px; position: relative;">
                    <label for="search_student"><strong>Search Student:</strong></label>
                    <input 
                        type="text" 
                        id="search_student" 
                        name="search_student" 
                        placeholder="Type student name or email..." 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                        autocomplete="off"
                    >
                    <div id="search_results" class="search-results"></div>
                    <input type="hidden" id="inside_user_id" name="inside_user_id">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="selected_student"><strong>Selected Student:</strong></label>
                    <div id="selected_student_display" style="padding: 10px; background: #e3f2fd; border-radius: 4px; display: none;">
                        <span id="selected_student_name"></span>
                        <button type="button" onclick="clearSelection()" style="float: right; background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">×</button>
                    </div>
                    <div id="no_selection" style="padding: 10px; background: #f5f5f5; border-radius: 4px; color: #666;">No student selected</div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="relationship"><strong>Your Relationship to Student:</strong></label>
                    <select 
                        id="relationship" 
                        name="relationship" 
                        required
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                    >
                        <option value="">-- Select Relationship --</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Grandfather">Grandfather</option>
                        <option value="Grandmother">Grandmother</option>
                        <option value="Uncle">Uncle</option>
                        <option value="Aunt">Aunt</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <button type="submit" id="submit_btn" disabled style="background: #4caf50; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                    Submit Connection Request
                </button>
            </form>
        </div>
    </div>

    <script>
        let selectedStudent = null;

        // Search functionality
        document.getElementById('search_student').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsDiv = document.getElementById('search_results');

            if (query.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            fetch("{{ route('outsideuser.connections.search') }}?query=" + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.users && data.users.length > 0) {
                        resultsDiv.innerHTML = '';
                        data.users.forEach(user => {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.innerHTML = `<strong>${user.fullname || user.first_name + ' ' + user.last_name}</strong><br><small>${user.email}</small>`;
                            item.onclick = () => selectStudent(user);
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.style.display = 'none';
                });
        });

        function selectStudent(user) {
            selectedStudent = user;
            document.getElementById('inside_user_id').value = user.id;
            document.getElementById('search_student').value = user.fullname || user.first_name + ' ' + user.last_name;
            document.getElementById('search_results').style.display = 'none';
            
            document.getElementById('selected_student_display').style.display = 'block';
            document.getElementById('selected_student_name').textContent = user.fullname || user.first_name + ' ' + user.last_name + ' (' + user.email + ')';
            document.getElementById('no_selection').style.display = 'none';
            document.getElementById('submit_btn').disabled = false;
        }

        function clearSelection() {
            selectedStudent = null;
            document.getElementById('inside_user_id').value = '';
            document.getElementById('search_student').value = '';
            document.getElementById('selected_student_display').style.display = 'none';
            document.getElementById('no_selection').style.display = 'block';
            document.getElementById('submit_btn').disabled = true;
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(event) {
            const searchDiv = document.getElementById('search_student');
            const resultsDiv = document.getElementById('search_results');
            if (!searchDiv.contains(event.target) && !resultsDiv.contains(event.target)) {
                resultsDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
