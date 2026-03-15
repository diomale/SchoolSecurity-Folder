<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Columban College Security System</title>
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
    <style>
        /* Tab Styles */
        .tab-container { margin-top: 20px; }
        .tab-buttons { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 0; }
        .tab-button { padding: 12px 24px; border: none; background: #f0f0f0; cursor: pointer; border-radius: 8px 8px 0 0; font-size: 14px; font-weight: 600; color: #666; transition: all 0.3s; }
        .tab-button:hover { background: #e0e0e0; }
        .tab-button.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .tab-content { display: none; animation: fadeIn 0.3s ease-in; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Events Grid - Multiple Events Support */
        .events-container { 
            max-height: 650px; 
            overflow-y: auto; 
            padding: 10px;
            scrollbar-width: thin;
            scrollbar-color: #667eea #f0f0f0;
        }
        .events-container::-webkit-scrollbar { width: 8px; }
        .events-container::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 4px; }
        .events-container::-webkit-scrollbar-thumb { background: #667eea; border-radius: 4px; }
        .events-container::-webkit-scrollbar-thumb:hover { background: #764ba2; }
        
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .event-card { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s; display: flex; flex-direction: column; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        .event-card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .event-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .event-date-badge { background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 4px; font-size: 12px; display: inline-block; margin-bottom: 10px; }
        .event-title { margin: 0 0 10px 0; font-size: 18px; font-weight: bold; }
        .event-description { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        .event-info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px; }
        .event-info-row:last-child { border-bottom: none; }
        .event-creator { color: #666; font-size: 12px; margin-top: 10px; }
        .btn-register { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: 600; margin-top: auto; width: 100%; text-align: center; box-sizing: border-box; }
        .btn-register:hover { background: #218838; }
        .slots-info { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 15px; font-size: 13px; }
        .slots-available { color: #28a745; font-weight: bold; }
        .no-events { text-align: center; padding: 60px 20px; color: #666; }
        
        /* Pagination */
        .pagination-container { 
            position: sticky; 
            bottom: 0; 
            background: white; 
            padding: 15px 0; 
            border-top: 2px solid #eee;
            margin-top: 20px;
        }
        .pagination { display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; }
        .pagination a { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #007bff; transition: all 0.2s; }
        .pagination a:hover { background: #f0f0f0; }
        .pagination .active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-color: #667eea; }
        .pagination .disabled { opacity: 0.5; cursor: not-allowed; }
        
        /* Right Panel Container */
        .right-panel-content { padding: 20px; height: 100%; overflow-y: auto; }
        
        /* Events count badge */
        .events-count { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 5px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Columban College Inc, Security System</h1>
    <hr>

    <!-- MAIN FLEX CONTAINER -->
    <div class="main-content">

        <!-- LEFT PANEL: Login + Cards -->
        <div class="left-panel">
            <!-- Tab Buttons -->
            <div class="tab-buttons">
                <button class="tab-button active" onclick="switchTab('login')">Login</button>
                <button class="tab-button" onclick="switchTab('events')">
                    Public Events 
                    @if(isset($publicEvents) && $publicEvents->count() > 0)
                        <span class="events-count">{{ $publicEvents->total() }}</span>
                    @endif
                </button>
            </div>

            <!-- Login Tab Content -->
            <div id="login-tab" class="tab-content active">
                <!-- Login Form -->
                <div class="login-container">
                    <h2>Log in to CCSS</h2>

                    <form method="POST" action="{{ route('insideuser.login.submit') }}">
                        @csrf

                        <label>Email: </label>
                        <input type="email" name="email" required placeholder="email">

                        <br>

                        <label>Password: </label>
                        <input type="password" name="password" required placeholder="password">

                        <br>
                        @error('email')
                            <p style="color:red">{{ $message }}</p>
                        @enderror

                        @if (session('success'))
                            <p style="color:green">{{ session('success') }}</p>
                        @endif

                        <button type="submit">Login</button>
                        <br>
                    </form>
                </div>

                <!-- Visitor + Student-Staff Cards -->
                <div class="bottom-cards">
                    <div class="visitors-card">
                        <h3>Visitor Registration (Parents/Guests)</h3>
                        <p>Register to request visits and get QR code access</p>
                        <a href="{{ route('outsideuser.signup.show') }}">Register as Visitor</a> |
                        <a href="{{ route('outsideuser.login.show') }}">Visitor Login</a>
                    </div>

                    <div class="student-staff-card">
                        <p>Are you a Student or Staff? <a href="{{ route('user.login.show') }}">Login Here</a></p>
                        <p>Are you a Visitor? <a href="{{ route('outsideuser.login.show') }}">Login Here</a></p>
                    </div>
                </div>
            </div>

            <!-- Events Tab Content -->
            <div id="events-tab" class="tab-content">
                <div class="events-container">
                    @if(isset($publicEvents) && $publicEvents->count() > 0)
                        <div class="events-grid">
                            @foreach($publicEvents as $event)
                                <div class="event-card">
                                    <div class="event-card-header">
                                        <span class="event-date-badge">
                                            {{ $event->event_date->format('M d, Y') }}
                                        </span>
                                        <h3 class="event-title">{{ $event->event_name }}</h3>
                                    </div>
                                    <div class="event-card-body">
                                        <p class="event-description">
                                            {{ Str::limit($event->event_description, 100) ?? 'No description available.' }}
                                        </p>
                                        
                                        <div class="event-info-row">
                                            <span>Time:</span>
                                            <strong>{{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}</strong>
                                        </div>
                                        <div class="event-info-row">
                                            <span>Limit:</span>
                                            <strong>{{ $event->alien_user_limit }} participants</strong>
                                        </div>
                                        <div class="event-info-row">
                                            <span>Deadline:</span>
                                            <strong>{{ $event->qr_request_deadline->format('M d, Y') }}</strong>
                                        </div>

                                        <div class="slots-info">
                                            @php
                                                $registeredCount = $event->registrations_count;
                                                $availableSlots = $event->alien_user_limit - $registeredCount;
                                            @endphp
                                            @if($availableSlots > 0)
                                                <span class="slots-available">{{ $availableSlots }} slots available</span>
                                            @else
                                                <span style="color: #dc3545; font-weight: bold;">Event Full</span>
                                            @endif
                                            <span>/ {{ $event->alien_user_limit }}</span>
                                        </div>

                                        @if($event->insideUser)
                                            <p class="event-creator">
                                                Created by: {{ $event->insideUser->fullname }}
                                            </p>
                                        @endif

                                        @if($availableSlots > 0)
                                            <a href="{{ route('public.event.register', $event->id) }}" class="btn-register">
                                                Register Now
                                            </a>
                                        @else
                                            <button class="btn-register" style="background: #6c757d; cursor: not-allowed;" disabled>
                                                Event Full
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($publicEvents->hasMorePages())
                            <div class="pagination-container">
                                <div class="pagination">
                                    {{ $publicEvents->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="no-events">
                            <h3 style="color: #999; margin: 0 0 10px 0;">No Public Events Available</h3>
                            <p>Check back later for upcoming events.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Reserved for Image -->
        <div class="right-panel">
            <img src="" alt="Columban Logo">
        </div>

    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(function(button) {
                button.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
