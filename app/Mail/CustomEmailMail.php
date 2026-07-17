<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CustomEmailMail extends Mailable
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $body,
        public ?string $tripNumber = null,
    ) {
    }

    public function envelope(): Envelope
    {
        $prefix = $this->tripNumber ? "[{$this->tripNumber}] " : '';
        return new Envelope(
            subject: $prefix . $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.custom',
        );
    }
}
