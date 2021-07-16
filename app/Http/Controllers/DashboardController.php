<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\CarrierPayment;
use App\Models\Charge;
use App\Models\Expense;
use App\Models\Load;
use App\Models\Loan;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getData(Request $request)
    {
        $start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();
        $loads = Load::whereBetween('loads.date', [$start, $end])
            ->where(function ($q) {
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id);
            })
            ->get();

        $loadsSummary = [];
        foreach ($loads as $load) {
            if (isset($loadsSummary[$load->status]))
                $loadsSummary[$load->status]++;
            else
                $loadsSummary[$load->status] = 1;
        }

        return [
            'loads' => $loadsSummary
        ];
    }

    public function testKernel()
    {
        DB::transaction(function () {
            $new_expenses = [];
            $charges = Charge::with('carriers')
                ->get();
            $all_carriers = [];
            $carbon_now = Carbon::now();
            // Set charges to create expenses
            foreach ($charges as $charge) {
                // If there are no related carriers it means it's a charge for all carriers
                if (count($charge->carriers) === 0) {
                    // All carriers are queried only if at least one of the charges is for all carriers, and it's not queried again
                    if (count($all_carriers) === 0)
                        $all_carriers = Carrier::get();
                    // Set the selected carriers as all carriers
                    $selected_carriers = $all_carriers;
                } else {
                    // Set the selected carriers as the ones designated on the charge
                    $selected_carriers = $charge->carriers;
                }
                // Loop through all the carriers and create the corresponding expense array
                foreach ($selected_carriers as $item) {
                    $new_expenses[] = [
                        "amount" => $charge->amount,
                        "description" => $charge->description,
                        "non_editable" => true,
                        "carrier_id" => $item->id,
                        "created_at" => $carbon_now,
                        "updated_at" => $carbon_now,
                    ];
                }
                // If the charge period was "single" it only happens once and it's deleted
                if ($charge->period === "single")
                    $charge->delete();
            }
            // Query all pending loans
            $loans = Loan::with('carrier')
                ->whereNull('is_paid')
                ->get();
            foreach ($loans as $loan) {
                // Set the paid amount variable calculated with the fee percentage and number of installments
                $paid_amount = ($loan->amount / $loan->installments) * (1 + ($loan->fee_percentage / 100));
                // Update the paid installments to +1
                $loan->paid_installments++;
                // Set the paid amount quantity
                $loan->paid_amount += $paid_amount;
                if ($loan->paid_installments === $loan->installments)
                    $loan->is_paid = 1;
                // Update the loan data
                $loan->save();
                $new_expenses[] = [
                    "amount" => $paid_amount,
                    "description" => "Payment #$loan->paid_installments - $loan->description",
                    "non_editable" => true,
                    "carrier_id" => $loan->carrier->id,
                    "created_at" => $carbon_now,
                    "updated_at" => $carbon_now,
                ];
            }
            Expense::insert($new_expenses);
            $carrier_payments = [];
            $loads = Load::whereNull('carrier_payment_id')
                ->whereHas('driver')
                //->where('status', 'finished') //TODO: CHECK IN WHICH STATUS
                ->with([
                    'shipper',
                    'driver.carrier',
                ])
                ->get();
            $rates = [];
            $carriersId = [];
            foreach ($loads as $load) {
                $carrier_id = $load->driver->carrier_id;
                $carriersId[] = $carrier_id;
                $shipper_id = $load->shipper_id;
                $zone_id = $load->driver->zone_id;
                $load_mileage = $load->mileage;
                if (!$load_mileage)
                    $load_mileage = 0;
                $rate = null;
                // Check if rate has already been queried before in the loop
                foreach ($rates as $item) {
                    $current_rate = $item['rate'];
                    if ($current_rate->shipper_id === $shipper_id && $current_rate->zone_id === $zone_id &&
                        $load_mileage >= $current_rate->start_mileage && $load_mileage <= $current_rate->end_mileage) {
                        $rate = $current_rate;
                    } else if ($item["flag"] === 'min' && $load_mileage < $current_rate->start_mileage) {
                        $rate = $current_rate;
                    } else if ($item["flag"] === 'max' && $load_mileage > $current_rate->end_mileage) {
                        $rate = $current_rate;
                    }
                }
                // If no rate has previously been queried
                if (!$rate) {
                    // Find the rate of the load mileage between the start and end mileage
                    $rate = Rate::where('shipper_id', $shipper_id)
                        ->where('zone_id', $zone_id)
                        ->where(function ($q) use ($load_mileage) {
                            $q->where('start_mileage', '<=', $load_mileage)
                                ->where('end_mileage', '>=', $load_mileage);
                        })
                        ->first();
                    $flag = null;
                    // If the mileage was not between the mileage of any rate find if it's lower than the lowest mileage
                    if (!$rate) {
                        $rate = Rate::where('start_mileage', '>', $load_mileage)
                            ->orderBy('start_mileage', 'ASC')
                            ->first();
                        $flag = 'min';
                    }
                    // Or if it was not lower, find if it's higher than the highest mileage
                    if (!$rate) {
                        $rate = Rate::where('end_mileage', '<', $load_mileage)
                            ->orderBy('end_mileage', 'DESC')
                            ->first();
                        $flag = 'max';
                    }
                    // Save the rate to an array to possibly save futher queries from happening for the same rate
                    $rates[] = ['rate' => $rate, 'flag' => $flag];
                }
                if (!isset($carrier_payments[$carrier_id])) {
                    $carrier_payments[$carrier_id] = [
                        'load_count' => 1,
                        'loops' => 0,
                    ];
                }
                // Limits payments to 40 loads
                /*if ($carrier_payments[$carrier_id]['load_count'] === 40) {
                    $carrier_payments[$carrier_id]['load_count'] = 0;
                    $carrier_payments[$carrier_id]['loops']++;
                }*/
                $loops = $carrier_payments[$carrier_id]['loops'];
                //$load_count = $carrier_payments[$carrier_id]['load_count'];
                // If the first iteration of the 40 loads set, create a new carrier payment object
                /*if ($load_count === 1)
                    $carrier_payments[$carrier_id]['load_groups'][$loops]['payment'] = (new CarrierPayment())->save();*/
                // Set the load total sum
                /*if (isset($carrier_payments[$carrier_id]['load_groups'][$loops]['load_total']))
                    $carrier_payments[$carrier_id]['load_groups'][$loops]['load_total'] += $rate->carrier_rate;
                else
                    $carrier_payments[$carrier_id]['load_groups'][$loops]['load_total'] = $rate->carrier_rate;*/
                // Save the load to the load set and the corresponding rate
                $carrier_payments[$carrier_id]['load_groups'][$loops]['loads'][] = ['load' => $load, 'rate' => $rate];
                // Update the loud counter
                $carrier_payments[$carrier_id]['load_count']++;
            }
            // Get all pending expenses
            $expenses = Expense::whereNull('carrier_payment_id')
                ->whereIn('carrier_id', $carriersId)
                ->with('carrier')
                //->orderBy('amount', 'ASC')
                ->get();
            foreach ($expenses as $expense) {
                $carrier_payments[$expense->carrier_id]['expenses'][] = $expense;
            }
            // Iterate through the carrier payments array to generate the payments
            foreach ($carrier_payments as $carrier_id => $payment) {
                // Iterate through the load grouping
                foreach ($payment['load_groups'] as $iteration => $group) {
                    // Create the new carrier payment
                    $carrier_payment = new CarrierPayment();
                    $carrier_payment->date = $carbon_now;
                    $carrier_payment->carrier_id = $carrier_id;
                    $carrier_payment->save();
                    // Init the gross amount variable
                    $gross_amount = 0;
                    // Calculate the gross amount and save the relation on loads
                    foreach ($group['loads'] as $item) {
                        $item['load']->carrier_payment_id = $carrier_payment->id;
                        $item['load']->save();
                        $gross_amount += $item['rate']->carrier_rate;
                    }
                    // Init the expense amount variable
                    $expense_amount = 0;
                    foreach ($payment['expenses'] as $idx => $expense) {
                        $expense_amount += $expense->amount;
                        // If the expense amount is bigger than the gross amount
                        if ($expense_amount > $gross_amount) {
                            $expense_amount -= $expense->amount;
                            continue;
                        }
                        // Save the carrier payment id to the expense
                        $expense->carrier_payment_id = $carrier_payment->id;
                        $expense->save();
                        // Remove the expense from the array, the ones not removed end up as the
                        // pending expenses
                        unset($payment['expenses'][$idx]);
                    }
                    // Save the carrier payment data
                    $carrier_payment->gross_amount = $gross_amount;
                    $carrier_payment->reductions = $expense_amount;
                    $carrier_payment->total = $gross_amount - $expense_amount;
                    $carrier_payment->save();
                }
            }
        });
    }
}
