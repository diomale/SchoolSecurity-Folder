<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Approved</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">✓ Event Approved!</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Hello <strong>{{ $event->insideUser->fullname }}</strong>,
                            </p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                                Great news! Your event has been approved by the admin. You can now start accepting registrations from participants.
                            </p>
                            
                            <!-- Event Details Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h2 style="color: #333333; font-size: 20px; margin: 0 0 15px 0; text-align: center;">Event Details</h2>
                                        
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>📝 Event Name:</strong> {{ $event->event_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>📅 Date:</strong> {{ $event->event_date->format('l, F d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>🕐 Time:</strong> {{ $event->event_start_time->format('g:i A') }} - {{ $event->event_end_time->format('g:i A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>👥 Participant Limit:</strong> {{ $event->alien_user_limit }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>⏰ Registration Deadline:</strong> {{ $event->qr_request_deadline->format('F d, Y g:i A') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            @if($event->admin_remarks)
                            <!-- Admin Remarks -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #e7f3ff; border-left: 4px solid #2196f3; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="color: #0d47a1; font-size: 14px; margin: 0;">
                                            <strong>📋 Admin Remarks:</strong>
                                        </p>
                                        <p style="color: #0d47a1; font-size: 14px; margin: 10px 0 0 0;">
                                            {{ $event->admin_remarks }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif
                            
                            <!-- Next Steps -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h3 style="color: #333333; font-size: 18px; margin: 0 0 15px 0;">Next Steps:</h3>
                                        <ol style="color: #666666; font-size: 14px; line-height: 2; margin: 0; padding-left: 20px;">
                                            <li>Share your event registration link with participants</li>
                                            <li>Monitor registrations from your dashboard</li>
                                            <li>Register walk-in participants if needed</li>
                                            <li>Download participant list before the event</li>
                                            <li>Coordinate with security guards for QR scanning</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="text-align: center; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <a href="{{ url('/insideuser/events/' . $event->id) }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; font-weight: bold;">
                                            View Event Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 30px 0 0 0;">
                                Congratulations on your approved event! We hope it's a great success.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center;">
                            <p style="color: #666666; font-size: 14px; margin: 0 0 10px 0;">
                                This email was sent from School Security System
                            </p>
                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                © {{ date('Y') }} School Security System. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
