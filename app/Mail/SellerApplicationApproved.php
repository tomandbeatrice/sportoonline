<?php

namespace App\Mail;

use App\Models\SellerApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerApplicationApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public SellerApplication $application,
        public User $user
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Satıcı Başvurunuz Onaylandı! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.seller-application-approved',
            with: [
                'application' => $this->application,
                'user' => $this->user,
                'loginUrl' => url('/seller/dashboard'),
                'resetPasswordUrl' => url('/password/reset'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
