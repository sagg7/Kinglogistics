<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCarrierPayments extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pdf;
    public $carrier;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, $carrier)
    {
        $this->pdf = $pdf;
        $this->carrier = $carrier;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->attachData($this->pdf, 'name.pdf', [
            'mime' => 'application/pdf',
        ])
            ->subject("Payment - " . $this->carrier->name)
            ->view('mails.emptyMail');
    }
}
