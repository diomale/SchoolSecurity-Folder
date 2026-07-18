<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connected Parents - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css', 'resources/css/InsideUser/insideuser_style_connections.css'])
</head>
<body>
    <div class="dashboard-container">
        
        <!-- Sidebar Navigation -->
        @include('InsideUser.partials.sidebar', ['activePage' => 'connected_parents'])

        <!-- Main Content Area -->
        <main class="main-content">
            
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Connected <span class="highlight">Parents</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Manage the parents/guardians who have access to your entry/exit logs.</p>
                </div>
            </header>

            <div class="content-grid fade-in" style="animation-delay: 0.2s;">
                <div class="glass-card span-2">
                    <div class="flex-between mb-4">
                        <h3 class="section-title" style="margin-bottom:0; padding-bottom:0; border:none;">My Connections</h3>
                        <a href="{{ route('insideuser.connection.requests') }}" class="btn btn-primary btn-sm">View Requests</a>
                    </div>
                    
                    @if($connectedParents->count() > 0)
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Relationship</th>
                                        <th>Connected Since</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($connectedParents as $parent)
                                    <tr>
                                        <td><strong>{{ $parent->fullname ?? 'N/A' }}</strong></td>
                                        <td>{{ $parent->email ?? 'N/A' }}</td>
                                        <td>{{ $parent->phone_number ?? 'N/A' }}</td>
                                        <td><span class="relationship-badge">{{ $parent->pivot->relationship }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($parent->pivot->approved_at)->format('M d, Y') }}</td>
                                        <td>
                                            <form action="{{ route('insideuser.connection.cancel', $parent->pivot->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-cancel" onclick="return confirm('Are you sure you want to cancel this connection?')">Cancel Connection</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"></div>
                            <h4>No Connected Parents Yet</h4>
                            <p>You haven't accepted any parent connection requests. When someone requests to connect with you, you'll see it in your Connection Requests page.</p>
                            <br>
                            <a href="{{ route('insideuser.connection.requests') }}" class="btn btn-primary">Check Pending Requests</a>
                        </div>
                    @endif
                </div>
            </div>

        </main>
    </div>
</body>
</html>
