<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentsAndCollection;
use App\Models\Expense;
use App\Models\Load;
use App\Models\ShipperInvoice;
use App\Traits\Accounting\PaymentsAndCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   // use PaymentsAndCollection;
    protected $customDate = "2021-12-05";

    public function index()
    {
        return view('dashboard.dashboard');
    }

    public function getData(Request $request)
    {
        /*$start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();*/
        //whereBetween('loads.date', [$start, $end])

        $monday = new Carbon('last monday');
        $monday = $monday->format('Y/m/d')." 00:00:00";
        $loads = Load::where(function ($q) use ($request) {
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
            ->join('load_statuses', 'loads.id', '=', 'load_id')
            ->where(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), '>', $monday)
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
                'loads.id',
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
        DB::transaction(function () {
            $carbon_now = Carbon::now();
            $loads = Load::join('drivers', 'drivers.id', '=', 'driver_id')
                ->whereNull('shipper_invoice_id')
                ->whereHas('driver')
                ->whereHas('shipper', function($q) {
                    // FILTER FOR PAYMENT DAYS CONFIG OF SHIPPER
                    $q->whereRaw("FIND_IN_SET(".Carbon::now()->weekday().",payment_days)");
                })
                ->whereHas('loadStatus', function ($q) use ($carbon_now) {
                    $q->whereDate('finished_timestamp', '<=', $this->customDate);
                    //$q->whereDate('finished_timestamp', '<=', $carbon_now);
                })
                ->whereNotNull('inspected')
                ->where('loads.status', 'finished')
                ->with([
                    'shipper',
                    'trip.rate',
                ])
                ->orderBy('drivers.carrier_id')->orderBy('driver_id')->orderBy('loads.date')->get('loads.*');

            $rates = [];
            $shipper_invoices = [];
            foreach ($loads as $load) {
                //$carrier_id = $load->driver->carrier_id;
                $shipper_id = $load->shipper_id;

                $trip_pos = "trip_$load->trip_id";
                // Shipper invoices
                if (!isset($shipper_invoices[$shipper_id][$trip_pos])) {
                    $rate = $load->trip->rate ?? $this->handleRates($rates, $load)['rate'];
                    $shipper_invoices[$shipper_id][$trip_pos] = [
                        'load_count' => 1,
                        'loops' => 0,
                        'rate' => $rate,
                    ];
                }
                $loops = $shipper_invoices[$shipper_id][$trip_pos]['loops'];
                // Limits payments to 40 loads
                if ($shipper_invoices[$shipper_id][$trip_pos]['load_count'] === 40) {
                    $shipper_invoices[$shipper_id][$trip_pos]['load_count'] = 0;
                    $shipper_invoices[$shipper_id][$trip_pos]['loops']++;
                }
                // Update the load counter
                $shipper_invoices[$shipper_id][$trip_pos]['load_groups'][$loops]['loads'][] = $load;
                $shipper_invoices[$shipper_id][$trip_pos]['load_count']++;
            }
            foreach ($shipper_invoices as $shipper_id => $invoice) {
                // Iterate through the load grouping
                foreach ($invoice as $trip) {
                    foreach ($trip['load_groups'] as $group) {
                        if (count($group['loads']) > 0) {
                            $shipper_invoice = new ShipperInvoice();
                            $shipper_invoice->date = $this->customDate;
                            //$shipper_invoice->date = $carbon_now;
                            $shipper_invoice->shipper_id = $shipper_id;
                            $shipper_invoice->save();
                            $invoice_total = 0;
                            foreach ($group['loads'] as $item) {
                                $load = Load::find($item->id);
                                $load->shipper_invoice_id = $shipper_invoice->id;
                                $load->shipper_rate = $trip['rate']->shipper_rate;
                                $load->save();
                                $invoice_total += $trip['rate']->shipper_rate;
                            }
                            $shipper_invoice->total = $invoice_total;
                            $shipper_invoice->save();
                            // Create commission expense
                            $expense = new Expense();
                            $expense->amount = (1.5 * $invoice_total) / 100;
                            $expense->type_id = 1; // Hardcoded value that represents the "Invoice Commission"
                            $expense->description = "Invoice commission";
                            $expense->date = $carbon_now;
                            $expense->shipper_invoice_id = $shipper_invoice->id;
                            $expense->save();
                        }
                    }
                }
            }
        });
        //$this->carrierPayments();
        //ProcessPaymentsAndCollection::dispatch()->afterCommit();
        //$this->shipperInvoices();
        echo "Listoo";
    }
}
