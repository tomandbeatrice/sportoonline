<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShippingUpdate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişiniz Kargoya Verildi - #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.shipping-update',
            with: [
                'order' => $this->order,
                'trackingUrl' => $this->getTrackingUrl(),
            ],
        );
    }

    private function getTrackingUrl(): string
    {
        $trackingUrls = [
            'Yurtiçi Kargo' => 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . $this->order->tracking_number,
            'Aras Kargo' => 'https://kargotakip.araskargo.com.tr/mainpage.aspx?code=' . $this->order->tracking_number,
            'MNG Kargo' => 'https://www.mngkargo.com.tr/takip/' . $this->order->tracking_number,
            'PTT Kargo' => 'https://gonderitakip.ptt.gov.tr/Track/Verify?q=' . $this->order->tracking_number,
            'Sürat Kargo' => 'https://www.suratkargo.com.tr/kargom-nerede/' . $this->order->tracking_number,
        ];

        return $trackingUrls[$this->order->shipping_company] ?? '#';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
