<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - CCSS</title>
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
                    <h1 class="fade-in">Edit <span class="highlight">Event</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Modify event details. Changes may require re-approval or affect registered participants.</p>
                </div>
            </header>

            <div class="fade-in" style="animation-delay: 0.2s;">
                <div class="glass-card" style="max-width: 900px;">
                    <form action="{{ route('insideuser.events.update', $event->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="event_name">Event Name <span class="required">*</span></label>
                            <input type="text" id="event_name" name="event_name" value="{{ old('event_name', $event->event_name) }}" required>
                            @error('event_name')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_description">Event Description</label>
                            <textarea id="event_description" name="event_description" rows="4">{{ old('event_description', $event->event_description) }}</textarea>
                            @error('event_description')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="event-form-grid">
                            <div class="form-group">
                                <label for="event_date">Start Date <span class="required">*</span></label>
                                <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                                @error('event_date')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_end_date">End Date</label>
                                <input type="date" id="event_end_date" name="event_end_date" value="{{ old('event_end_date', $event->event_end_date?->format('Y-m-d')) }}">
                                <div class="help-text">Leave empty for single-day events</div>
                                @error('event_end_date')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_start_time">Start Time <span class="required">*</span></label>
                                <input type="time" id="event_start_time" name="event_start_time" value="{{ old('event_start_time', $event->event_start_time->format('H:i')) }}" required>
                                @error('event_start_time')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="event_end_time">End Time <span class="required">*</span></label>
                                <input type="time" id="event_end_time" name="event_end_time" value="{{ old('event_end_time', $event->event_end_time->format('H:i')) }}" required>
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
                                <input type="datetime-local" id="qr_request_deadline" name="qr_request_deadline" value="{{ old('qr_request_deadline', $event->qr_request_deadline->format('Y-m-d\TH:i')) }}" required>
                                @error('qr_request_deadline')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="alien_user_limit">Participant Limit <span class="required">*</span></label>
                                <input type="number" id="alien_user_limit" name="alien_user_limit" value="{{ old('alien_user_limit', $event->alien_user_limit) }}" required min="1" max="500">
                                <div class="help-text">Maximum number of guest participants allowed</div>
                                @error('alien_user_limit')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                            <div style="display: flex; align-items: flex-start; gap: 12px; background: rgba(79, 70, 229, 0.05); padding: 15px; border-radius: var(--radius-sm); border: 1px dashed rgba(79, 70, 229, 0.2);">
                                <input type="checkbox" id="show_on_welcome" name="show_on_welcome" value="1" {{ old('show_on_welcome', $event->show_on_welcome) ? 'checked' : '' }} style="width: 20px; height: 20px; margin-top: 2px; cursor: pointer;">
                                <div>
                                    <label for="show_on_welcome" style="margin-bottom: 4px; cursor: pointer;">Show on Welcome Page</label>
                                    <p class="help-text" style="margin-top: 0;">If checked, this event will be visible on the public welcome page for guest registration once approved.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert-info-box" style="background: var(--warning-light); border-left-color: var(--warning); color: #B45309;">
                            <strong>Note:</strong> Changes to approved events may affect registered participants.
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Event</button>
                            <a href="{{ route('insideuser.events.show', $event->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
