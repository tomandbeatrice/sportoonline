<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnShippingCodeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ReturnRequest $returnRequest;
    public string $shippingCode;
    public string $carrier;

    /**
     * Create a new message instance.
     */
    public function __construct(ReturnRequest $returnRequest, string $shippingCode, string $carrier)
    {
        $this->returnRequest = $returnRequest;
        $this->shippingCode = $shippingCode;
        $this->carrier = $carrier;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('İade Kargo Kodu - ' . $this->returnRequest->return_number)
            ->markdown('emails.return-shipping-code', [
                'returnRequest' => $this->returnRequest,
                'shippingCode' => $this->shippingCode,
                'carrier' => $this->carrier,
                'trackingUrl' => $this->getTrackingUrl()
            ]);
    }

    protected function getTrackingUrl(): string
    {
        $carriers = [
            'aras' => 'https://kargotakip.araskargo.com.tr/mainpage.aspx',
            'yurtici' => 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula',
            'mng' => 'https://www.mngkargo.com.tr/takip',
            'ptt' => 'https://gonderitakip.ptt.gov.tr/',
        ];

        return $carriers[strtolower($this->carrier)] ?? '#';
    }
}
