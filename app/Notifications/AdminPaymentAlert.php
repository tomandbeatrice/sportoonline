<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPaymentAlert extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Yeni Ödeme Alındı')
            ->line("Kullanıcı #{$this->payment->user_id} tarafından {$this->payment->amount}₺ tutarında ödeme alındı.")
            ->line("Yöntem: {$this->payment->method}")
            ->line("Sipariş ID: {$this->payment->order_id}")
            ->line("Durum: {$this->payment->status}")
            ->line("Ödendiği Zaman: {$this->payment->paid_at}")
            ->action('Admin Paneli', url('/admin/payments'));
    }
}