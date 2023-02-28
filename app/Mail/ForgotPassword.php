<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $url,
        public string $token
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Forgot Password',
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'forgot_password',
        );
    }

    public function attachments()
    {
        return [];
    }
}
