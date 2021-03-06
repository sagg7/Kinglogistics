<?php

namespace App\Console;

use App\Enums\DriverAppRoutes;
use App\Models\BotAnswers;
use App\Models\BotQuestions;
use App\Models\DispatchSchedule;
use App\Models\Driver;
use App\Models\DriverWorkedHour;
use App\Models\Shift;
use App\Models\PaperworkFile;
use App\Traits\Accounting\PaymentsAndCollection;
use App\Traits\Chat\MessagesTrait;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\Notifications\PushNotificationsTrait;
use App\Traits\Paperwork\PaperworkSendEmailAlert;
use App\Traits\Ranking\RankingTrait;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    use PaymentsAndCollection, GetSelectionData, PushNotificationsTrait, MessagesTrait, RankingTrait, PaperworkSendEmailAlert;

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

        $schedule->call(function () {
            Storage::deleteDirectory('temp');
            Storage::deleteDirectory('public/temp');
            $drivers = Driver::whereHas('shift', function ($q) {
                $q->where('created_at', '<', Carbon::parse('-24 hours'));
            })
                ->whereDoesntHave('active_load')
                ->with([
                    'shift',
                    'workedHour' => function ($q) {
                        $q->whereNull('shift_end');
                    }
                ])
                ->get();
            if ($drivers) {
                $shiftsToDelete = [];
                $workedHoursToDelete = [];
                foreach ($drivers as $driver) {
                    $shiftsToDelete[] = $driver->shift->id;
                    foreach ($driver->workedHour as $item) {
                        $workedHoursToDelete[] = $item->id;
                    }
                }
                if (count($shiftsToDelete) > 0) {
                    Shift::whereIn('id', $shiftsToDelete)->delete();
                }
                if (count($workedHoursToDelete) > 0) {
                    DriverWorkedHour::whereIn('id', $workedHoursToDelete)->delete();
                }
            }
        })->daily();

        // On Mondays at 00:00 hours of the day, change the dispatch schedule to the programmed next week
        $schedule->call(function () {
            DB::transaction(function () {
                // Check if there's at least one record for next week schedule
                $next = DispatchSchedule::where('status', 'next')->first();
                if ($next) { // If there are records for next week
                    // Delete the current schedule
                    DispatchSchedule::where('status', 'current')
                        ->delete();
                    // Activate setting status "current" for the rest of the schedule
                    DispatchSchedule::where('status', 'next')
                        ->update(['status' => 'current']);
                }
            });
        })->weekly()->mondays()->at('00:00');

        // On Mondays at 00:01 hours of the day, calculate Carriers Ranking of the past week
        $schedule->call(function () {
            $this->calculateRanking();
        })->weekly()->mondays()->at('00:01');

        // Notifications of days of expiration paperwork
        $schedule->call(function () {
            $days30 = Carbon::now()->addDays(30);
            $days15 = Carbon::now()->addDays(15);
            $days3 = Carbon::now()->addDays(3);
            $days = Carbon::now();
            
            $query30 = PaperworkFile::whereDate('expiration_date', $days30)
                ->get();
            $query15 = PaperworkFile::whereDate('expiration_date', $days15)
                ->get();
            $query3 = PaperworkFile::whereDate('expiration_date', $days3)
                ->get();
            $query = PaperworkFile::whereDate('expiration_date', $days)
                ->get();
            
            // dd($query, $query30,$query15,$query3);
            foreach($query30 as $data){
                $data['day'] = 30;
                $this->NotificationPaperworkAlert($data);
            }
            foreach($query15 as $data){
                $data['day'] = 15;           
                $this->NotificationPaperworkAlert($data);    
            }
            foreach($query3 as $data){
                $data['day'] = 3;           
                $this->NotificationPaperworkAlert($data);
            }
            foreach ($query as  $data){
                $data['day'] = 0;            
                $this->NotificationPaperworkAlert($data);
            }
        })->daily()->at('15:00');
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
