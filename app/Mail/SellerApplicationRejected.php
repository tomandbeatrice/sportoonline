<?php

namespace App\Mail;

use App\Models\SellerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerApplicationRejected extends Mailable implements ShouldQueue
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
            subject: 'Satıcı Başvurunuz Hakkında',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.seller-application-rejected',
            with: [
                'application' => $this->application,
                'contactUrl' => url('/contact'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
