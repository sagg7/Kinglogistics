<?php

namespace App\Traits\Accounting;

use App\Enums\CarrierPaymentEnum;
use App\Enums\PeriodEnum;
use App\Mail\SendCarrierPayments;
use App\Models\Bonus;
use App\Models\Carrier;
use App\Models\CarrierExpense;
use App\Models\CarrierPayment;
use App\Models\Charge;
use App\Models\Expense;
use App\Models\Incident;
use App\Models\Load;
use App\Models\Loan;
use App\Models\Rate;
use App\Models\Rental;
use App\Models\ShipperInvoice;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mpdf\MpdfException;

trait PaymentsAndCollection
{
    protected $customDate = "2021-10-17";

    use CarrierPaymentsPDF;
    /**
     * @param $load_mileage
     * @param int $shipper_id
     * @param int $zone_id
     * @return array
     */
    private function getRate($load_mileage, int $shipper_id, int $zone_id)
    {
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
            $rate = Rate::where('shipper_id', $shipper_id)
                ->where('zone_id', $zone_id)
                ->where('start_mileage', '>', $load_mileage)
                ->orderBy('start_mileage', 'ASC')
                ->first();
            $flag = 'min';
        }
        // Or if it was not lower, find if it's higher than the highest mileage
        if (!$rate) {
            $rate = Rate::where('shipper_id', $shipper_id)
                ->where('zone_id', $zone_id)
                ->where('end_mileage', '<', $load_mileage)
                ->orderBy('end_mileage', 'DESC')
                ->first();
            $flag = 'max';
        }

        return ['rate' => $rate, 'flag' => $flag];
    }

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
            // Save the rate to an array to possibly save further queries from happening for the same rate
            $rate = $this->getRate($load_mileage, $shipper_id, $zone_id);
            $rates[] = $rate;
        }
        return $rate;
    }

    private function shipperInvoices()
    {
        DB::transaction(function () {
            $carbon_now = Carbon::now();
            $loads = Load::join('drivers', 'drivers.id', '=', 'driver_id')
                ->whereNull('shipper_invoice_id')
                ->whereHas('driver')
                /*->whereHas('shipper', function($q) {
                    // FILTER FOR PAYMENT DAYS CONFIG OF SHIPPER
                    $q->whereRaw("FIND_IN_SET(".Carbon::now()->weekday().",payment_days)");
                })*/
                ->whereHas('loadStatus', function ($q) use ($carbon_now) {
                    $q->whereDate('finished_timestamp', '<=', $this->customDate);
                    //$q->whereDate('finished_timestamp', '<=', $carbon_now);
                })
                ->whereNotNull('inspected')
                //->whereDate('date', '<=', $this->customDate)
                ->where('status', 'finished')
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
                            //$shipper_invoice->date = $this->customDate;
                            $shipper_invoice->date = $carbon_now;
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
    }

    private function chargeRentals()
    {
        DB::transaction(function () {
            $rentals = Rental::with('trailer')
                ->where('status', 'rented')
                ->whereNull('finished_at')
                ->get();

            $today = Carbon::today();
            $new_expenses = [];
            foreach ($rentals as $rental) {
                $last_date = $rental->charge_date;
                if (!$last_date)
                    $last_date = $rental->created_at;
                switch ($rental->period) {
                    case PeriodEnum::WEEKLY:
                        $last_date->addWeek();
                        break;
                    case PeriodEnum::MONTHLY:
                        $last_date->addMonth();
                        break;
                    case PeriodEnum::ANNUAL:
                        $last_date->addYear();
                        break;
                }
                if ($last_date->lessThanOrEqualTo($today)) {
                    $new_expenses[] = [
                        "amount" => $rental->cost,
                        "description" => "Rental for trailer " . $rental->trailer->number,
                        "non_editable" => true,
                        "carrier_id" => $rental->carrier_id,
                        "date" => $today,
                        "created_at" => $today,
                        "updated_at" => $today,
                    ];
                    $rental->charge_date = $today;
                    $rental->save();
                }
            }
            CarrierExpense::insert($new_expenses);
        });
    }

    private function carrierPayments()
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

                $amount = $charge->amount;
                switch ($charge->period) {
                    // If the charge period was "single" it only happens once and it's deleted
                    case PeriodEnum::SINGLE:
                        $charge->delete();
                        break;
                    case PeriodEnum::CUSTOM:
                        // Update the number of paid weeks
                        $charge->paid_weeks++;
                        // Set the charge amount divided by the number of weeks
                        $amount /= $charge->custom_weeks;
                        // If the charge is fully paid, delete it
                        if ($charge->paid_weeks === $charge->custom_weeks)
                            $charge->delete();
                        else // Else just update the paid weeks amount
                            $charge->save();
                        break;
                }

                // Loop through all the carriers and create the corresponding expense array
                foreach ($selected_carriers as $item) {
                    $new_expenses[] = [
                        "amount" => $amount,
                        "description" => $charge->description,
                        "date" => $charge->date,
                        "gallons" => $charge->gallons,
                        "non_editable" => true,
                        "carrier_id" => $item->id,
                        "created_at" => $carbon_now,
                        "updated_at" => $carbon_now,
                    ];
                }
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
                ->whereHas('loadStatus', function ($q) use ($carbon_now) {
                    //$q->whereDate('finished_timestamp', '<=', $this->customDate);
                    $q->whereDate('finished_timestamp', '<=', $carbon_now);
                })
                ->whereNotNull('inspected')
                //->whereDate('date', '<=', $this->customDate)
                ->with([
                    'shipper',
                    'driver.carrier',
                    'trip.rate',
                ])
                ->get();
            $rates = [];
            $carriersId = [];
            foreach ($loads as $load) {
                $carrier_id = $load->driver->carrier_id;
                $carriersId[] = $carrier_id;
                $rate = $load->trip->rate ?? $this->handleRates($rates, $load)['rate'];
                // Save the load to the load set and the corresponding rate
                if (!$load->carrier_payment_id)
                    $carrier_payments[$carrier_id]['loads'][] = ['load' => $load, 'rate' => $rate];
            }
            if (!(count($carrier_payments) > 0))
                return;
            // Get all pending expenses
            $expenses = CarrierExpense::whereNull('carrier_payment_id')
                ->whereIn('carrier_id', $carriersId)
                ->with('carrier')
                //->orderBy('amount', 'ASC')
                ->get();
            foreach ($expenses as $expense) {
                $carrier_payments[$expense->carrier_id]['expenses'][] = $expense;
            }
            $bonuses = Bonus::with('carriers')
                ->whereHas('carriers', function ($q) {
                    $q->whereNull('carrier_payment_id');
                })
                ->get();
            foreach ($bonuses as $bonus) {
                foreach ($bonus->carriers as $carrier) {
                    $carrier_payments[$carrier->id]['bonuses'][] = $bonus;
                }
            }
            // Iterate through the carrier payments array to generate the payments
            foreach ($carrier_payments as $carrier_id => $payment) {
                // Create the new carrier payment
                $carrier_payment = new CarrierPayment();
                //$carrier_payment->date = $this->customDate;
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
                if (isset($payment['bonuses']))
                    foreach ($payment['bonuses'] as $bonus) {
                        if (count($bonus->carriers) === 0) {
                            // If all carriers selected on bonus, set the carrier payment id on the main table
                            $bonus->carrier_payment_id = $carrier_payment->id;
                            $bonus->save();
                        } else {
                            // Else save the carrier payment id on the pivot table
                            foreach ($bonus->carriers as $item) {
                                $item->pivot->carrier_payment_id = $carrier_payment->id;
                                $item->pivot->save();
                            }
                        }
                        $gross_amount += $bonus->amount;
                    }
                if (isset($payment['expenses']))
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
        });
    }

    private function emailPayments(){
        $carrier_payments = CarrierPayment::with('carrier:id,invoice_email,name')
            ->where('status', CarrierPaymentEnum::APPROVED)
            ->get();
        foreach ($carrier_payments as $item) {
            if ($item->carrier->invoice_email) {
                $emails = explode(',', $item->carrier->invoice_email);
                try {
                    $pdf = $this->getPDFBinary($item->id);
                    foreach ($emails as $email) {
                        Mail::to($email)->send(new SendCarrierPayments($item->carrier, $pdf));
                    }
                } catch (MpdfException $e) {
                    continue;
                }
            }
            $item->status = CarrierPaymentEnum::COMPLETED;
            $item->save();
        }
    }
}
