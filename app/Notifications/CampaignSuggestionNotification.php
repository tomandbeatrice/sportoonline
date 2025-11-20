<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CampaignSuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(public array $suggestions) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Haftalık Kampanya Önerileri',
            'suggestions' => $this->suggestions
        ];
    }
}