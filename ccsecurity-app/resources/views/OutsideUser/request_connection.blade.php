<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Connection - School Security</title>
    @vite(['resources/css/OutsideUSerStyleFolder/outside_user_request_connection.css'])
</head>
<body>
    <div class="request-container">
        
        <!-- Header -->
        <div class="page-header">
            <div class="header-title">
                <h1>Request Child Connection</h1>
                <p>Connect with your child to track their entry and exit at school</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('outsider.dashboard') }}" class="btn btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.5rem;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('outsideuser.connections.history') }}" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.5rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Connection History
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <div style="display:flex; flex-direction:column;">
                <strong style="display:flex; align-items:center; gap:0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Please fix the following errors:
                </strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Connected Children Section -->
        @if($connectedChildren->count() > 0)
        <div class="glass-card">
            <div class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Your Connected Children
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student Name & Email</th>
                            <th>Relationship</th>
                            <th>QR Status</th>
                            <th>Connected Since</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($connectedChildren as $child)
                        <tr>
                            <td>
                                <div>
                                    <span style="display:block; font-weight:600; color:var(--text-main);">{{ $child->fullname }}</span>
                                    <span style="font-size:0.85rem; color:var(--text-muted);">{{ $child->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="relationship-tag">{{ $child->pivot->relationship }}</span>
                            </td>
                            <td>
                                @if($child->qr_status === 'active')
                                    <span class="status-badge status-active">ACTIVE</span>
                                @else
                                    <span class="status-badge status-inactive">INACTIVE</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ \Carbon\Carbon::parse($child->pivot->approved_at)->format('M d, Y') }}<br>
                                    <small>{{ \Carbon\Carbon::parse($child->pivot->approved_at)->format('h:i A') }}</small>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Pending Requests Section -->
        @if($pendingRequests->count() > 0)
        <div class="glass-card">
            <div class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--warning);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Pending Requests
            </div>
            
            <div class="info-box">
                <strong><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg> How it works:</strong>
                <ol>
                    <li>After you submit a request, the student will review it</li>
                    <li>Once the student accepts, you're immediately connected!</li>
                    <li>You can then view their entry/exit records in your dashboard</li>
                </ol>
            </div>
            
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student</th>
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
                            <td>
                                <div>
                                    <span style="display:block; font-weight:600; color:var(--text-main);">{{ $request->insideUser->fullname ?? 'N/A' }}</span>
                                    <span style="font-size:0.85rem; color:var(--text-muted);">{{ $request->insideUser->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="relationship-tag">{{ $request->relationship }}</span>
                            </td>
                            <td>
                                @if($request->inside_user_approval === 'accepted')
                                    <span class="status-badge status-approved">Accepted</span>
                                @elseif($request->inside_user_approval === 'rejected')
                                    <span class="status-badge status-rejected">Rejected</span>
                                @else
                                    <span class="status-badge status-pending">Awaiting Student</span>
                                @endif
                            </td>
                            <td>
                                @if($request->status === 'approved')
                                    <span class="status-badge status-approved">Connected</span>
                                @elseif($request->status === 'rejected')
                                    <span class="status-badge status-rejected">Rejected</span>
                                @elseif($request->inside_user_approval === 'accepted')
                                    <span class="status-badge status-approved">Auto-approved</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    {{ $request->created_at->format('M d, Y') }}<br>
                                    <small>{{ $request->created_at->format('h:i A') }}</small>
                                </span>
                            </td>
                            <td>
                                @if($request->inside_user_approval === 'pending')
                                    <form action="{{ route('outsideuser.connections.cancel', $request->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-danger">Cancel</button>
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
        </div>
        @endif

        <!-- Request Form Section -->
        <div class="glass-card">
            <div class="section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                Request New Connection
            </div>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Search for your child by name or email to request a connection</p>
            
            <form action="{{ route('outsideuser.connections.submit') }}" method="POST">
                @csrf
                
                <div class="search-container">
                    <label for="search_student" class="form-label">Search Student</label>
                    <input 
                        type="text" 
                        id="search_student" 
                        name="search_student" 
                        placeholder="Type student name or email..." 
                        class="form-control"
                        autocomplete="off"
                    >
                    <div id="search_results" class="search-results"></div>
                    <input type="hidden" id="inside_user_id" name="inside_user_id">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Selected Student</label>
                    <div id="selected_student_display" class="selected-display">
                        <span id="selected_student_name"></span>
                        <button type="button" onclick="clearSelection()" class="btn-icon-cancel">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    <div id="no_selection" class="no-selection">No student selected</div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="relationship" class="form-label">Your Relationship to Student</label>
                    <select id="relationship" name="relationship" required class="form-control">
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

                <button type="submit" id="submit_btn" disabled class="btn btn-primary">
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
                            item.innerHTML = `<strong>${user.fullname || user.first_name + ' ' + user.last_name}</strong><small>${user.email}</small>`;
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
            
            document.getElementById('selected_student_display').style.display = 'flex';
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
