<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCarrierPayments extends Mailable
{
    use SerializesModels;

    public $carrier;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($carrier, $pdf)
    {
        $this->carrier = $carrier;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->attachData($this->pdf, 'Invoice.pdf', [
            'mime' => 'application/pdf',
        ])
            ->subject("Payment - " . $this->carrier->name)
            ->view('mails.emptyMail');
    }
}
