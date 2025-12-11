<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Invitation $invitation;
    public string $inviterName;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation, string $inviterName)
    {
        $this->invitation = $invitation;
        $this->inviterName = $inviterName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $acceptUrl = config('app.url') . '/invitation/accept?code=' . $this->invitation->code;

        return $this->subject('SportoOnline Daveti')
            ->markdown('emails.invitation', [
                'inviterName' => $this->inviterName,
                'invitationCode' => $this->invitation->code,
                'acceptUrl' => $acceptUrl,
                'expiresAt' => $this->invitation->expires_at->format('d.m.Y H:i')
            ]);
    }
}
