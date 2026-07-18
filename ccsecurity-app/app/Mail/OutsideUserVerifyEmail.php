<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutsideUserVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email - CCSS',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    private function buildHtml(): string
    {
        $name = e($this->user->first_name);
        $url = e($this->verificationUrl);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: 'Outfit', Arial, sans-serif; background: #060606; color: #e2e8f0; margin: 0; padding: 40px 20px; }
                .card { max-width: 500px; margin: 0 auto; background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 40px; }
                h1 { font-size: 1.6rem; color: #fff; margin-bottom: 16px; text-align: center; }
                p { color: #94a3b8; line-height: 1.7; font-size: 0.95rem; }
                .btn { display: block; text-align: center; padding: 16px 32px; background: #fff; color: #000; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 1rem; margin: 28px 0; }
                .note { font-size: 0.8rem; color: #64748b; text-align: center; word-break: break-all; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Verify Your Email</h1>
                <p>Hi {$name},</p>
                <p>Thanks for registering for the Columban College Security System. Please verify your email address to activate your account.</p>
                <a href="{$url}" class="btn">Verify Email Address</a>
                <p class="note">If the button doesn't work, copy and paste this link:<br>{$url}</p>
                <p style="margin-top: 24px; color: #64748b; font-size: 0.8rem; text-align: center;">This link expires in 24 hours.</p>
            </div>
        </body>
        </html>
        HTML;
    }
}
