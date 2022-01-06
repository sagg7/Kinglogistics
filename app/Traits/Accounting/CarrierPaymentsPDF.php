<?php

namespace App\Traits\Accounting;

use App\Traits\Carrier\Payment\PaymentExportData;
use Mpdf\Mpdf;

trait CarrierPaymentsPDF
{
    use PaymentExportData;

    private function generatePDF($id)
    {
        $data = $this->getPaymentExportData($id);

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/app/logos/logo.png') . ' alt="Logo"></div>');

        $title = $data['carrierPayment']->date->startOfWeek()->day . "-" . $data['carrierPayment']->date->endOfWeek()->day . " " . $data['carrierPayment']->date->format('F') . " " . $data['carrierPayment']->date->year;
        if ($data['carrierPayment']->status === "charges") {
            $title = "PAID CHARGES WEEK " . $data['carrierPayment']->date->startOfWeek()->day . "-" . $data['carrierPayment']->date->endOfWeek()->day . " " . $data['carrierPayment']->date->format('F') . " " . $data['carrierPayment']->date->year;
            $html = view('exports.carrierPayments.chargesPdf', array_merge(compact('title'), $data));
            $orientation = 'P';
        } else {
            $title = "PAYMENT WEEK " . $title;
            $html = view('exports.carrierPayments.pdf', array_merge(compact('title'), $data));
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
        return $this->generatePDF($id)->Output('', 'S');
    }
}
