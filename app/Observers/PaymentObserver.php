<?php

namespace App\Observers;

use App\Models\Payment;
use App\Notifications\AdminPaymentAlert;
use Illuminate\Support\Facades\Notification;

class PaymentObserver
{
    public function updated(Payment $payment)
    {
        if ($payment->wasChanged('status') && $payment->status === 'paid') {
            // 🎯 Kampanya tetikleme
            if ($payment->amount >= 500) {
                // Özel kampanya tetiklenebilir
                // CampaignService::triggerForUser($payment->user_id);
            }

            // 📢 Admin'e bildirim gönder
            Notification::route('mail', 'admin@panel.local')
                ->notify(new AdminPaymentAlert($payment));
        }
    }
}