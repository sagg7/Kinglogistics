<?php

namespace App\Console;

use App\Traits\Accounting\PaymentsAndCollection;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use PaymentsAndCollection;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // DAILY CHECK IF NEEDED TO BE
        $schedule->call(function () {
            $this->chargeRentals();
            $this->shipperInvoices();
        })->daily()->at('00:00');

        // EACH MONDAY AT 3AM GENERATE PAYMENTS AND CHARGES FOR CARRIERS
        $schedule->call(function () {
            $this->carrierPayments();
        })->weekly()->mondays()->at('00:00');

        // EACH MONDAY AT 8AM SEND EMAILS FOR CARRIERS
        $schedule->call(function () {
            $this->emailPayments();
        })->weekly()->mondays()->at('08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
