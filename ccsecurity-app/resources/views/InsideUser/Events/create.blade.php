<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - CCSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/InsideUser/insideuser_style_dashboard.css', 'resources/css/InsideUser/insideuser_style_events.css'])
</head>
<body>
    <div class="dashboard-container">
        
        @include('InsideUser.partials.sidebar', ['activePage' => 'events'])

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">Create <span class="highlight">Event</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Fill in the details to create your event, requiring admin approval.</p>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                <div class="glass-card" style="max-width: 900px;">
                    <form action="{{ route('insideuser.events.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="event_name">Event Name <span class="required">*</span></label>
                            <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" required placeholder="e.g., Annual School Fair 2026">
                            @error('event_name')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_description">Event Description</label>
                            <textarea id="event_description" name="event_description" rows="4" placeholder="Describe your event...">{{ old('event_description') }}</textarea>
                            @error('event_description')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="event-form-grid">
                            <div class="form-group">
                                <label for="event_date">Start Date <span class="required">*</span></label>
                                <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required min="{{ date('Y-m-d') }}">
                                @error('event_date')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_end_date">End Date</label>
                                <input type="date" id="event_end_date" name="event_end_date" value="{{ old('event_end_date') }}">
                                <div class="help-text">Leave empty for single-day events</div>
                                @error('event_end_date')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_start_time">Start Time <span class="required">*</span></label>
                                <input type="time" id="event_start_time" name="event_start_time" value="{{ old('event_start_time') }}" required>
                                @error('event_start_time')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_end_time">End Time <span class="required">*</span></label>
                                <input type="time" id="event_end_time" name="event_end_time" value="{{ old('event_end_time') }}" required>
                                @error('event_end_time')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <script>
                            document.getElementById('event_date').addEventListener('change', function() {
                                var endDate = document.getElementById('event_end_date');
                                endDate.min = this.value;
                                if (endDate.value && endDate.value < this.value) {
                                    endDate.value = this.value;
                                }
                            });
                        </script>

                        <div class="event-form-grid">
                            <div class="form-group">
                                <label for="qr_request_deadline">QR Registration Deadline <span class="required">*</span></label>
                                <input type="datetime-local" id="qr_request_deadline" name="qr_request_deadline" value="{{ old('qr_request_deadline') }}" required min="{{ date('Y-m-d\TH:i') }}">
                                <div class="help-text">Last date and time for participants to request QR codes</div>
                                @error('qr_request_deadline')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="alien_user_limit">Participant Limit <span class="required">*</span></label>
                                <input type="number" id="alien_user_limit" name="alien_user_limit" value="{{ old('alien_user_limit', 50) }}" required min="1" max="500">
                                <div class="help-text">Maximum number of guest participants allowed</div>
                                @error('alien_user_limit')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert-info-box">
                            <strong>Important:</strong> After submission, your event will be reviewed by an admin. Once approved, participants can register and receive their QR codes via email.
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                            <a href="{{ route('insideuser.events.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
