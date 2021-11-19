<?php

namespace App\Jobs;

use App\Models\BotAnswers;
use App\Models\BotQuestions;
use App\Models\Load;
use App\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BotLoadReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $drivers_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($drivers_id)
    {
        $this->drivers_id = $drivers_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $drivers = Load::whereIn($this->drivers_id)->where("status", "!=", 'finished')->pluck('id')->toArray();

        $driverWithNoLoads = array_diff($this->drivers_id, $drivers);

        $content = BotQuestions::find(7)->question;// ¿Aún no recibes carga?

        foreach ($driverWithNoLoads as $driver_id){
            $shift = Shift::where('driver', $driver_id)->first();
            if ($shift) {
                $botAnswer = BotAnswers::where('driver_id', $driver_id)->first();
                if (!$botAnswer)
                    $botAnswer = new BotAnswers();

                $botAnswer->bot_question_id = 7;
                $botAnswer->incorrect = 0;
                $botAnswer->driver_id = $driver_id;
                $botAnswer->save();
            } else {
                $driverWithNoLoads = array_splice($driverWithNoLoads, $driver_id);
            }
        }

        $request = new Request(['drivers'=>$driverWithNoLoads,'message'=> $content,'user_id'=>null,'image' => null, 'is_bot_sender'=> 1 ]);

        app(\App\Http\Controllers\ChatController::class)->sendMessageAsUser($request);
    }
}
