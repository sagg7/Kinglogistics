<?php

namespace App\Traits\Load;

use App\Enums\CarrierPaymentEnum;
use App\Enums\ShipperInvoiceEnum;
use App\Models\Rate;

trait RecalculateTotals
{
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
            $item->load(['expenses', 'bonuses', 'loads']);
            $gross_amount = 0;
            $reductions = 0;
            foreach ($item->loads as $load) {
                $gross_amount += $load->rate;
            }
            foreach ($item->bonuses as $bonus) {
                $gross_amount += $bonus->amount;
            }
            foreach ($item->expenses as $expense) {
                $reductions += $expense->amount;
            }
            $item->gross_amount = $gross_amount;
            $item->reductions = $reductions;
            $item->total = $gross_amount - $reductions;
            $item->save();
        }
        foreach ($shipper_invoices as $item) {
            $item->load(['loads']);
            $total = 0;
            foreach ($item->loads as $load) {
                $total += $load->shipper_rate;
            }
            $item->total = $total;
            $item->save();
        }
    }
}
