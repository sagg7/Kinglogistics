<?php

namespace App\Traits\Accounting;

use App\Models\Carrier;
use App\Models\CarrierExpense;
use App\Models\CarrierPayment;
use App\Models\Charge;
use App\Models\Incident;
use App\Models\Load;
use App\Models\Loan;
use App\Models\Rate;
use App\Models\Rental;
use App\Models\ShipperInvoice;
use Carbon\Carbon;

trait PaymentsAndCollection
{

    /**
     * @param $rates
     * @param $load
     * @return null
     */
    private function handleRates(&$rates, $load)
    {
        $shipper_id = $load->shipper_id;
        $zone_id = $load->trip->zone_id;
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
            // Save the rate to an array to possibly save further queries from happening for the same rate
            $rates[] = ['rate' => $rate, 'flag' => $flag];
        }
        return $rate;
    }

    private function shipperInvoices()
    {
        $loads = Load::whereNull('shipper_invoice_id')
            ->whereHas('driver')
            ->whereHas('shipper', function($q) {
                // FILTER FOR PAYMENT DAYS CONFIG OF SHIPPER
                $q->whereRaw("FIND_IN_SET(".Carbon::now()->weekday().",payment_days)");
            })
            ->where('status', 'finished')
            ->with([
                'shipper',
                'trip',
            ])
            ->get();

        $rates = [];
        $shipper_invoices = [];
        foreach ($loads as $load) {
            $carrier_id = $load->driver->carrier_id;
            $shipper_id = $load->shipper_id;

            $rate = $this->handleRates($rates, $load);

            // Shipper invoices
            if (!isset($shipper_invoices[$shipper_id])) {
                $shipper_invoices[$shipper_id] = [
                    'load_count' => 1,
                    'loops' => 0,
                ];
            }
            $loops = $shipper_invoices[$shipper_id]['loops'];
            // Limits payments to 40 loads
            if ($shipper_invoices[$shipper_id]['load_count'] === 40) {
                $shipper_invoices[$shipper_id]['load_count'] = 0;
                $shipper_invoices[$shipper_id]['loops']++;
            }
            // Update the load counter
            if (!$load->shipper_invoice_id) {
                $shipper_invoices[$carrier_id]['load_groups'][$loops]['loads'][] = ['load' => $load, 'rate' => $rate];
                $shipper_invoices[$shipper_id]['load_count']++;
            }
        }
        $carbon_now = Carbon::now();
        foreach ($shipper_invoices as $shipper_id => $invoice) {
            // Iterate through the load grouping
            foreach ($invoice['load_groups'] as $iteration => $group) {
                $shipper_invoice = new ShipperInvoice();
                $shipper_invoice->date = $carbon_now;
                $shipper_invoice->shipper_id = $shipper_id;
                $shipper_invoice->save();
                $invoice_total = 0;
                foreach ($group['loads'] as $item) {
                    $item['load']->shipper_invoice_id = $shipper_invoice->id;
                    $item['load']->shipper_rate = $item['rate']->shipper_rate;
                    $item['load']->save();
                    $invoice_total += $item['rate']->shipper_rate;
                }
                $shipper_invoice->total = $invoice_total;
                $shipper_invoice->save();
            }
        }
    }

    private function chargeRentals()
    {
        $rentals = Rental::with('trailer')
            ->whereNull('finished_at')
            ->get();

        $today = Carbon::today();
        $new_expenses = [];
        foreach ($rentals as $rental) {
            $last_date = $rental->charge_date;
            if (!$last_date)
                $last_date = $rental->created_at;
            switch ($rental->period) {
                case 'weekly':
                    $last_date->addWeek();
                    break;
                case 'monthly':
                    $last_date->addMonth();
                    break;
                case 'annual':
                    $last_date->addYear();
                    break;
            }
            if ($last_date->lessThanOrEqualTo($today)) {
                $new_expenses[] = [
                    "amount" => $rental->cost,
                    "description" => "Rental for trailer " . $rental->trailer->number,
                    "non_editable" => true,
                    "carrier_id" => $rental->carrier_id,
                    "created_at" => $today,
                    "updated_at" => $today,
                ];
                $rental->charge_date = $today;
                $rental->save();
            }
        }
        CarrierExpense::insert($new_expenses);
    }

    private function carrierPayments()
    {
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
        $incidents = Incident::whereNull('was_charged')
            ->whereHas('incident_type', function ($q) {
                $q->whereNotNull('fine');
            })
            ->with(['incident_type', 'driver'])
            ->get();
        foreach ($incidents as $incident) {
            $incident->was_charged = 1;
            $incident->save();
            $new_expenses = [
                "amount" => $incident->incident_type->fine,
                "description" => "Safety Incident Fine: " . $incident->incident_type->name . " - " . $incident->driver->name,
                "non_editable" => true,
                "carrier_id" => $incident->carrier_id,
                "created_at" => $carbon_now,
                "updated_at" => $carbon_now,
            ];
        }
        CarrierExpense::insert($new_expenses);
        $carrier_payments = [];
        $loads = Load::whereNull('carrier_payment_id')
            ->whereHas('driver')
            ->where('status', 'finished')
            // CONDITION OF AT LEAST ONLY PAST WEEK LOADS
            ->whereHas('loadStatus', function ($q) {
                $q->whereDate('finished_timestamp', '<=', Carbon::now()->subWeeks(1));
            })
            ->with([
                'shipper',
                'driver.carrier',
                'trip',
            ])
            ->get();
        $rates = [];
        $carriersId = [];
        foreach ($loads as $load) {
            $carrier_id = $load->driver->carrier_id;
            $carriersId[] = $carrier_id;
            $rate = $this->handleRates($rates, $load);
            // Save the load to the load set and the corresponding rate
            if (!$load->carrier_payment_id)
                $carrier_payments[$carrier_id]['loads'][] = ['load' => $load, 'rate' => $rate];
        }
        // Get all pending expenses
        $expenses = CarrierExpense::whereNull('carrier_payment_id')
            ->whereIn('carrier_id', $carriersId)
            ->with('carrier')
            //->orderBy('amount', 'ASC')
            ->get();
        foreach ($expenses as $expense) {
            $carrier_payments[$expense->carrier_id]['expenses'][] = $expense;
        }
        // Iterate through the carrier payments array to generate the payments
        foreach ($carrier_payments as $carrier_id => $payment) {
            // Create the new carrier payment
            $carrier_payment = new CarrierPayment();
            $carrier_payment->date = $carbon_now;
            $carrier_payment->carrier_id = $carrier_id;
            $carrier_payment->save();
            // Init the gross amount variable
            $gross_amount = 0;
            // Init the expense amount variable
            $expense_amount = 0;
            // Calculate the gross amount and save the relation on loads
            foreach ($payment['loads'] as $item) {
                $item['load']->carrier_payment_id = $carrier_payment->id;
                $item['load']->rate = $item['rate']->carrier_rate;
                $item['load']->save();
                $gross_amount += $item['rate']->carrier_rate;
            }
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
                // Remove the expense from the array, the ones not removed end up as the pending expenses
                unset($payment['expenses'][$idx]);
            }
            // Save the carrier payment data
            $carrier_payment->gross_amount = $gross_amount;
            $carrier_payment->reductions = $expense_amount;
            $carrier_payment->total = $gross_amount - $expense_amount;
            $carrier_payment->save();
        }
    }
}
