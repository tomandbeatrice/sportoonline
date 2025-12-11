<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $statusLabels = [
            'pending' => 'Beklemede',
            'processing' => 'Hazırlanıyor',
            'shipped' => 'Kargoya Verildi',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi'
        ];

        $subject = 'Sipariş Durumu Güncellendi - #' . $this->order->id;

        return $this->subject($subject)
            ->markdown('emails.order-status', [
                'order' => $this->order,
                'oldStatusLabel' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
                'newStatusLabel' => $statusLabels[$this->newStatus] ?? $this->newStatus,
                'orderUrl' => config('app.url') . '/orders/' . $this->order->id
            ]);
    }
}
