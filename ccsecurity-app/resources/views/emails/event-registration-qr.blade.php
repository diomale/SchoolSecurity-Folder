<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event QR Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🎫 Your Event QR Code</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Hello <strong>{{ $registration->fullname }}</strong>,
                            </p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                                You are registered for <strong>{{ $registration->event->event_name }}</strong>. 
                                Present this QR code at the event entrance for check-in.
                            </p>
                            
                            <!-- Event Details Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h2 style="color: #333333; font-size: 20px; margin: 0 0 15px 0; text-align: center;">Event Details</h2>
                                        
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>📅 Event:</strong> {{ $registration->event->event_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>📆 Date:</strong> {{ $registration->event->event_date->format('l, F d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>🕐 Time:</strong> {{ $registration->event->event_start_time->format('g:i A') }} - {{ $registration->event->event_end_time->format('g:i A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px;">
                                                    <strong>📍 Location:</strong> School Security System Event
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- QR Code -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="text-align: center; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <p style="color: #666666; font-size: 14px; margin: 0 0 15px 0;">
                                            <strong>Your QR Code</strong>
                                        </p>
                                        <div style="display: inline-block; padding: 20px; background-color: #ffffff; border: 2px solid #667eea; border-radius: 8px;">
                                            {!! QrCode::size(250)->generate(route('security.event.scan', ['qr' => $registration->qr_code])) !!}
                                        </div>
                                        <p style="color: #999999; font-size: 12px; margin: 15px 0 0 0; font-family: monospace;">
                                            QR Code: <strong>{{ $registration->qr_code }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Important Information -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="color: #856404; font-size: 14px; margin: 0;">
                                            <strong>⚠️ Important:</strong> Please arrive at least 15 minutes before the event starts. 
                                            Make sure your QR code is clearly visible on your phone or printed out.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 30px 0 0 0;">
                                If you have any questions, please contact the event organizer.
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
