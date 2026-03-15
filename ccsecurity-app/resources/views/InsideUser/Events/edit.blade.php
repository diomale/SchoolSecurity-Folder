<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .btn { padding: 12px 24px; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-block { width: 100%; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .alert-warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 20px; }
        .form-actions { display: flex; gap: 15px; }
        .required { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;">Edit Event</h1>
                    <p style="margin: 5px 0 0 0; color: #666;">Update event details</p>
                </div>
                <a href="{{ route('insideuser.events.show', $event->id) }}" class="btn btn-secondary">← Back to Event</a>
            </div>

            <form action="{{ route('insideuser.events.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="event_name">Event Name <span class="required">*</span></label>
                    <input type="text" id="event_name" name="event_name" value="{{ old('event_name', $event->event_name) }}" required>
                    @error('event_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="event_description">Event Description</label>
                    <textarea id="event_description" name="event_description" rows="4">{{ old('event_description', $event->event_description) }}</textarea>
                    @error('event_description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label for="event_date">Event Date <span class="required">*</span></label>
                        <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                        @error('event_date')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="event_start_time">Start Time <span class="required">*</span></label>
                        <input type="time" id="event_start_time" name="event_start_time" value="{{ old('event_start_time', $event->event_start_time->format('H:i')) }}" required>
                        @error('event_start_time')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="event_end_time">End Time <span class="required">*</span></label>
                        <input type="time" id="event_end_time" name="event_end_time" value="{{ old('event_end_time', $event->event_end_time->format('H:i')) }}" required>
                        @error('event_end_time')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="qr_request_deadline">QR Registration Deadline <span class="required">*</span></label>
                    <input type="datetime-local" id="qr_request_deadline" name="qr_request_deadline" value="{{ old('qr_request_deadline', $event->qr_request_deadline->format('Y-m-d\TH:i')) }}" required>
                    @error('qr_request_deadline')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="alien_user_limit">Maximum Participants <span class="required">*</span></label>
                    <input type="number" id="alien_user_limit" name="alien_user_limit" value="{{ old('alien_user_limit', $event->alien_user_limit) }}" required min="1" max="500">
                    @error('alien_user_limit')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert-warning">
                    <strong>Note:</strong> Changes to approved events may affect registered participants.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Update Event</button>
                    <a href="{{ route('insideuser.events.show', $event->id) }}" class="btn btn-secondary btn-block">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
