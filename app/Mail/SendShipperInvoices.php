<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendShipperInvoices extends Mailable
{
    use SerializesModels;

    public $shipper;
    public $pdf;
    public $xlsx;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($shipper, $xlsx, $pdf)
    {
        $this->shipper = $shipper;
        $this->xlsx = $xlsx;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->attachData($this->xlsx, 'Invoice.xlsx', [
            'mime' => 'application/xlsx',
        ])->attachData($this->pdf, 'Invoice.pdf', [
            'mime' => 'application/pdf',
        ])
            ->subject("Invoice - " . $this->shipper->name)
            ->view('mails.emptyMail');
    }
}
