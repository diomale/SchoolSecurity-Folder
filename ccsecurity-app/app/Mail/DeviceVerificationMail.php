<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeviceVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;
    public $userEmail;
    public $deviceInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(string $verificationCode, string $userEmail, string $deviceInfo = '')
    {
        $this->verificationCode = $verificationCode;
        $this->userEmail = $userEmail;
        $this->deviceInfo = $deviceInfo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Device Verification - Columban College Security System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'mails.device-verification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
