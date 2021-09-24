<?php

namespace App\Traits\Accounting;

use App\Models\CarrierPayment;
use Mpdf\Mpdf;

trait CarrierPaymentsPDF
{
    private function generatePDF($id)
    {
        $carrierPayment = CarrierPayment::with([
            'carrier:id,name',
            'loads.driver.truck',
            'expenses.type',
            'bonuses.bonus_type',
        ])
            ->findOrFail($id);

        $expenses = [];
        foreach ($carrierPayment->expenses as $item) {
            if (!isset($expenses[$item->type_id]))
                $expenses[$item->type_id] = [
                    'name' => $item->type->name ?? '',
                    'amount' => (double)$item->amount,
                ];
            else
                $expenses[$item->type_id]['amount'] += (double)$item->amount;
        }
        $expenses = array_values($expenses);

        $bonuses = [];
        foreach ($carrierPayment->bonuses as $item) {
            if (!isset($bonuses[$item->bonus_type_id]))
                $bonuses[$item->bonus_type_id] = [
                    'name' => $item->bonus_type->name ?? '',
                    'amount' => (double)$item->amount,
                ];
            else
                $bonuses[$item->bonus_type_id]['amount'] += (double)$item->amount;
        }
        $bonuses = array_values($bonuses);

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/app/logos/logo.png') . ' alt="Logo"></div>');

        $title = $carrierPayment->date->startOfWeek()->day . "-" . $carrierPayment->date->endOfWeek()->day . " " . $carrierPayment->date->format('F') . " " . $carrierPayment->date->year;
        if ($carrierPayment->status === "charges") {
            $title = "PAID CHARGES WEEK " . $carrierPayment->date->startOfWeek()->day . "-" . $carrierPayment->date->endOfWeek()->day . " " . $carrierPayment->date->format('F') . " " . $carrierPayment->date->year;
            $html = view('exports.carrierPayments.chargesPdf', compact('title', 'carrierPayment'));
            $orientation = 'P';
        } else {
            $title = "PAYMENT WEEK " . $title;
            $html = view('exports.carrierPayments.pdf', compact('title', 'carrierPayment', 'expenses', 'bonuses'));
            $orientation = 'L';
        }
        $mpdf->AddPage($orientation, // L - landscape, P - portrait
            '', '', '', '',
            5, // margin_left
            5, // margin right
            22, // margin top
            22, // margin bottom
            3, // margin header
            0); // margin footer
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    private function getPDFBinary($id)
    {
        $mpdf = $this->generatePDF($id);
        return $mpdf->Output('', 'S');
    }
}
