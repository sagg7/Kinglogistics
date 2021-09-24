<?php

namespace App\Traits\Load;

use App\Enums\CarrierPaymentEnum;
use App\Enums\ShipperInvoiceEnum;
use App\Models\CarrierPayment;
use App\Models\Rate;
use App\Models\ShipperInvoice;

trait RecalculateTotals
{
    private function recalculateCarrierPayment(CarrierPayment $carrierPayment)
    {
        $carrierPayment->load(['expenses', 'bonuses', 'loads']);
        $gross_amount = 0;
        $reductions = 0;
        // Flag to check if the carrier payment loads have been reassgined to another payment
        $emptyLoads = count($carrierPayment->loads) === 0;
        foreach ($carrierPayment->loads as $load) {
            $gross_amount += $load->rate;
        }
        foreach ($carrierPayment->bonuses as $bonus) {
            if ($emptyLoads) {
                // If all the loads were reassigned, set the bonuses relation as null, so they can be reassigned
                // in the future to a new payment
                $bonus->pivot->carrier_payment_id = null;
                $bonus->pivot->save();
            } else {
                $gross_amount += $bonus->amount;
            }
        }
        foreach ($carrierPayment->expenses as $expense) {
            if ($emptyLoads) {
                // If all the loads were reassigned, set the expenses relation as null, so they can be reassigned
                // in the future to a new payment
                $expense->carrier_payment_id = null;
                $expense->save();
            } else {
                $reductions += $expense->amount;
                // If the change has caused that the reductions amount is bigger than the gross amount
                // then remove this expense from the payment setting the relation as null
                if ($reductions > $gross_amount) {
                    $reductions -= $expense->amount;
                    $expense->carrier_payment_id = null;
                    $expense->save();
                }
            }
        }
        if ($emptyLoads) {
            // Delete the carrier payment if there are no loads
            $carrierPayment->delete();
        } else {
            // Set the new recalculated values to the carrier payment
            $carrierPayment->gross_amount = $gross_amount;
            $carrierPayment->reductions = $reductions;
            $carrierPayment->total = $gross_amount - $reductions;
            $carrierPayment->save();
        }
    }

    private function recalculateShipperInvoices(ShipperInvoice $shipperInvoice)
    {
        $shipperInvoice->load(['loads']);
        $total = 0;
        foreach ($shipperInvoice->loads as $load) {
            $total += $load->shipper_rate;
        }
        $shipperInvoice->total = $total;
        $shipperInvoice->save();
    }

    private function byRateChange($trip, $rate_id)
    {
        $trip->load(['loads' => function ($q) {
            $q->whereHas('carrier_payment', function ($q) {
                $q->where('status', CarrierPaymentEnum::PENDING);
            })
                ->orWhereHas('shipper_invoice', function ($q) {
                    $q->where('status', ShipperInvoiceEnum::PENDING);
                })
                ->orWhereDoesntHave('carrier_payment')
                ->orWhereDoesntHave('shipper_invoice')
                ->with([
                    'carrier_payment' => function ($q) {
                        $q->where('status', CarrierPaymentEnum::PENDING);
                    },
                    'shipper_invoice' => function ($q) {
                        $q->where('status', ShipperInvoiceEnum::PENDING);
                    },
                ]);
        }]);
        $rate = Rate::find($rate_id);
        $carrier_payments = [];
        $shipper_invoices = [];
        foreach ($trip->loads as $load) {
            // If it doesn't have a carrier_payment_id OR if it has a carrier_payment_id AND the
            // carrier_payment was queried because its status is pending
            if (!$load->carrier_payment_id || ($load->carrier_payment_id && $load->carrier_payment)) {
                // If it has a carrier_payment, store the payment data in array
                if ($load->carrier_payment) {
                    if (!isset($carrier_payments[$load->carrier_payment->id]))
                        $carrier_payments[$load->carrier_payment->id] = $load->carrier_payment;
                }
                $load->rate = $rate->carrier_rate;
                $load->save();
            }

            // If it doesn't have a shipper_invoice_id OR if it has a shipper_invoice_id AND the
            // shipper_invoice was queried because its status is pending
            if (!$load->shipper_invoice_id || ($load->shipper_invoice_id && $load->shipper_invoice)) {
                // If it has a shipper_invoice, store the invoice data in array
                if ($load->shipper_invoice) {
                    if (!isset($shipper_invoices[$load->shipper_invoice->id]))
                        $shipper_invoices[$load->shipper_invoice->id] = $load->shipper_invoice;
                }
                $load->shipper_rate = $rate->shipper_rate;
                $load->save();
            }
        }
        foreach ($carrier_payments as $item) {
            $this->recalculateCarrierPayment($item);
        }
        foreach ($shipper_invoices as $item) {
            $this->recalculateShipperInvoices($item);
        }
    }
}
