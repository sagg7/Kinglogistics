<?php

namespace App\Jobs;

use App\Traits\Accounting\PaymentsAndCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentsAndCollection implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PaymentsAndCollection;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $date;
    public function __construct($date)
    {
        $this->date = $date;
        $this->onQueue('accounting');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->shipperInvoices($this->date);
        $this->carrierPayments($this->date);
    }
}
