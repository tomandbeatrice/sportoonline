<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnShippingCodeNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $returnRequest;

    public function __construct($returnRequest)
    {
        $this->returnRequest = $returnRequest;
    }

    public function build()
    {
        return $this->subject('Return Shipping Code')
                    ->view('emails.return-shipping-code');
    }
}
