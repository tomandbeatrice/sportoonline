<?php

namespace App\Mail;

use App\Models\SellerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerApplicationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public SellerApplication $application
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Satıcı Başvurunuz Alındı',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.seller-application-received',
            with: [
                'application' => $this->application,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
