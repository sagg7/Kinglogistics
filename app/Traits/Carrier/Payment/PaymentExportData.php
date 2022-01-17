<?php

namespace App\Traits\Carrier\Payment;

use App\Models\CarrierPayment;

trait PaymentExportData
{
    private function getPaymentExportData($id)
    {
        $carrierPayment = CarrierPayment::with([
            'carrier:id,name',
            'loads.driver.truck',
            'expenses.type',
            'bonuses.bonus_type',
        ])
            ->whereHas('carrier', function ($q) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            })
            ->findOrFail($id);

        $expenses = [];
        foreach ($carrierPayment->expenses as $item) {
            if (!isset($expenses[$item->type_id])) {
                $expenses[$item->type_id] = [
                    'name' => $item->type->name ?? '',
                    'amount' => (double)$item->amount,
                ];
            } else {
                $expenses[$item->type_id]['amount'] += (double)$item->amount;
            }
        }
        $expenses = array_values($expenses);

        $bonuses = [];
        foreach ($carrierPayment->bonuses as $item) {
            if (!isset($bonuses[$item->bonus_type_id])) {
                $bonuses[$item->bonus_type_id] = [
                    'name' => $item->bonus_type->name ?? '',
                    'amount' => (double)$item->amount,
                ];
            } else {
                $bonuses[$item->bonus_type_id]['amount'] += (double)$item->amount;
            }
        }
        $bonuses = array_values($bonuses);

        return compact('carrierPayment', 'expenses', 'bonuses');
    }
}
