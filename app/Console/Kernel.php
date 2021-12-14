<?php

namespace App\Console;

use App\Enums\DriverAppRoutes;
use App\Models\BotAnswers;
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
     * @param $drivers
     */
    private function welcomeDrivers($drivers): void
    {
        foreach ($drivers as $driver) {
            $content = str_replace("{{driver:name}}", $driver->name, BotQuestions::find(1)->question);//Hola {{driver:name}}, ¿Estas listo para trabajar? contesta: Si No

            $message = $this->sendMessage(
                $driver->id,
                $content,
                null,
                null,
                null,
                true,
                null,
                1,
            );

            $botAnswer = BotAnswers::where('driver_id', $driver->id)->first();
            if (!$botAnswer)
                $botAnswer = new BotAnswers();

            $botAnswer->bot_question_id = 1;
            $botAnswer->incorrect = 0;
            $botAnswer->driver_id = $driver->id;
            $botAnswer->save();

            $driver->status = 'pending';
            $driver->save();

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
        }
    }

    /**
     * @param $drivers
     */
    private function dischargeDrivers($drivers): void
    {
        foreach ($drivers as $driver) {
            $content = str_replace("{{driver:name}}", $driver->name, BotQuestions::find(3)->question);//Hola {{driver:name}}, Esperamos que haya sido un buen día, puedes terminar ahora tu turno, ¡Nos vemos mañana!

            $message = $this->sendMessage(
                $driver->id,
                $content,
                null,
                null,
                null,
                true,
                null,
                1,
            );

            $botAnswer = BotAnswers::where('driver_id', $driver->id)->first();
            if ($botAnswer)
                $botAnswer->delete();

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
        }
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
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

        // Welcome drivers with morning turn
        //$schedule->call(function () {
        //    $drivers = Driver::where('turn_id', 1)->whereNull('inactive')->where('status', 'inactive')->get();
//
        //    $this->welcomeDrivers($drivers);
        //})->daily()->at('5:30');
        //// Discharge drivers with night turn
        //$schedule->call(function () {
        //    $drivers = Driver::where('turn_id', 2)->whereNull('inactive')->where('status', 'inactive')->get();
//
        //    $this->dischargeDrivers($drivers);
        //})->daily()->at('6:15');
        //// Welcome drivers with night turn
        //$schedule->call(function () {
        //    $drivers = Driver::where('turn_id', 2)->whereNull('inactive')->where('status', 'inactive')->get();
//
        //    $this->welcomeDrivers($drivers);
        //})->daily()->at('17:30');
        //// Discharge drivers with morning turn
        //$schedule->call(function () {
        //    $drivers = Driver::where('turn_id', 1)->whereNull('inactive')->where('status', 'inactive')->get();
//
        //    $this->dischargeDrivers($drivers);
        //})->daily()->at('18:15');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
