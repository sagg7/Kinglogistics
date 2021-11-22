<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentsAndCollection;
use App\Models\BotAnswers;
use App\Models\BotQuestions;
use App\Models\Load;
use App\Models\Shift;
use App\Traits\Accounting\PaymentsAndCollection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PaymentsAndCollection;

    public function index()
    {
        return view('dashboard.dashboard');
    }

    public function getData(Request $request)
    {
        /*$start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();*/
        //whereBetween('loads.date', [$start, $end])
        $loads = Load::whereDoesntHave('shipper_invoice')
            ->where(function ($q) use ($request) {
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
                else if (auth()->guard('carrier')->check())
                    $q->whereHas('driver', function ($q) {
                        $q->where('carrier_id', auth()->user()->id);
                    });
                if ($request->shipper) {
                    $q->where('shipper_id', $request->shipper);
                }
                if ($request->trip) {
                    $q->where('trip_id', $request->trip);
                }
                if ($request->driver) {
                    $q->where('driver_id', $request->driver);
                }
            })
            ->with([
                'driver' => function ($q) {
                    $q->with([
                        'carrier:id,name',
                    ])
                        ->select([
                            'drivers.id',
                            'drivers.name',
                            'drivers.carrier_id',
                        ]);
                },
                'truck:id,number',
                'shipper:id,name',
                'load_type:id,name',
            ])
            ->get([
                'id',
                'date',
                'origin',
                'destination',
                'control_number',
                'customer_name',
                'customer_po',
                'customer_reference',
                'weight',
                'tons',
                'silo_number',
                'mileage',
                'shipper_id',
                'driver_id',
                'truck_id',
                'load_type_id',
                'status',
            ]);

        $loadsSummary = [];
        foreach ($loads as $load) {
            if (isset($loadsSummary[$load->status]))
                $loadsSummary[$load->status]["count"]++;
            else
                $loadsSummary[$load->status]["count"] = 1;
            $loadsSummary[$load->status]["data"][] = $load;
        }

        return [
            'loads' => $loadsSummary
        ];
    }

    public function testKernel()
    {
        $drivers = Load::whereIn('driver_id', [2])->where("status", "!=", 'finished')->pluck('id')->toArray();

        $driverWithNoLoads = array_diff([2], $drivers);

        $content = BotQuestions::find(7)->question;// ¿Aún no recibes carga?

        foreach ($driverWithNoLoads as $driver_id){
            $shift = Shift::where('driver_id', $driver_id)->first();
            if ($shift) {
                $botAnswer = BotAnswers::where('driver_id', $driver_id)->first();
                if (!$botAnswer)
                    $botAnswer = new BotAnswers();

                $botAnswer->bot_question_id = 7;
                $botAnswer->answer = null;
                $botAnswer->incorrect = 0;
                $botAnswer->driver_id = $driver_id;
                $botAnswer->save();
            } else {
                $driverWithNoLoads = array_splice($driverWithNoLoads, $driver_id);
            }
        }

        $request = new Request(['drivers'=>$driverWithNoLoads,'message'=> $content,'user_id'=>null,'image' => null, 'is_bot_sender'=> 1 ]);

        app(\App\Http\Controllers\ChatController::class)->sendMessageAsUser($request);
        //$this->carrierPayments();
        //ProcessPaymentsAndCollection::dispatch()->afterCommit();
    }
}
