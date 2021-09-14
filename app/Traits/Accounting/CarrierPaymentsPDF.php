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
            'expenses',
        ])
            ->findOrFail($id);

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/logo.png') . ' alt="Logo"></div>');

        $title = $carrierPayment->date->startOfWeek()->day . "-" . $carrierPayment->date->endOfWeek()->day . " " . $carrierPayment->date->format('F') . " " . $carrierPayment->date->year;
        if ($carrierPayment->status === "charges") {
            $title = "PAID CHARGES WEEK " . $carrierPayment->date->startOfWeek()->day . "-" . $carrierPayment->date->endOfWeek()->day . " " . $carrierPayment->date->format('F') . " " . $carrierPayment->date->year;
            $html = view('exports.carrierPayments.chargesPdf', compact('title', 'carrierPayment'));
            $orientation = 'P';
        } else {
            $title = "PAYMENT WEEK " . $title;
            $html = view('exports.carrierPayments.pdf', compact('title', 'carrierPayment'));
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
