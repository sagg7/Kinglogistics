<?php

namespace App\Console;

use App\Enums\DriverAppRoutes;
use App\Models\BotAnwers;
use App\Models\BotQuestions;
use App\Models\Driver;
use App\Traits\Accounting\PaymentsAndCollection;
use App\Traits\Chat\MessagesTrait;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\Notifications\PushNotificationsTrait;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    use PaymentsAndCollection, GetSelectionData, PushNotificationsTrait, MessagesTrait;
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
        //$schedule->call(function () {
        //    $this->chargeRentals();
        //    $this->shipperInvoices();
        //    Storage::deleteDirectory('temp');
        //    Storage::deleteDirectory('public/temp');
        //})->daily()->at('00:00');
//
        //// EACH MONDAY AT 3AM GENERATE PAYMENTS AND CHARGES FOR CARRIERS
        //$schedule->call(function () {
        //    $this->carrierPayments();
        //})->weekly()->mondays()->at('00:00');
//
        //// EACH MONDAY AT 8AM SEND EMAILS FOR CARRIERS
        //$schedule->call(function () {
        //    $this->emailPayments();
        //})->weekly()->mondays()->at('08:00');

        $schedule->call(function () {

            $drivers =  [2];
            $user_id = null;
            $image = null;

            $messages = [];
            foreach ($drivers as $driver_id) {
                $driver = Driver::find($driver_id);

                $content = "Hola $driver->name, ¿Estas listo para trabajar? \n contesta: \n Si \n No";

                $message = $this->sendMessage(
                    $driver_id,
                    $content,
                    $user_id,
                    null,
                    null,
                    true,
                    $image,
                    1,
                );

                $botAnswers = new BotAnwers();

                $botAnswers->content = 1;
                $botAnswers->driver_id = $driver_id;

                $botAnswers->save();
                $driver->status = 'pending';
                $driver = 1;
                $driverDevices = $this->getUserDevices($driver);

                $this->sendNotification(
                    'Message from King',
                    $content,
                    $driverDevices,
                    DriverAppRoutes::CHAT,
                    $message,
                    DriverAppRoutes::CHAT_ID,
                );
                $messages[] = $message;
            }
        })->weekly()->mondays()->at('13:59');
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
