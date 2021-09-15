<?php

namespace App\Traits\Accounting;

use App\Models\Broker;
use App\Models\ShipperInvoice;
use Mpdf\Mpdf;

trait ShipperInvoicesPDF
{
    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    private function generatePDF($id)
    {
        $shipperInvoice = ShipperInvoice::with([
            'shipper:id,name',
            'loads.driver.truck',
        ])
            ->findOrFail($id);

        $broker = Broker::findOrFail($this->broker_id);

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/app/logos/logo.png') . ' alt="Logo"></div>');

        $title = "Shipper Invoice - " . $shipperInvoice->shipper->name ."  - " . $shipperInvoice->date->format('m/d/Y');
        $html = view('exports.shipperInvoices.pdf', compact('title', 'shipperInvoice', 'broker'));
        $orientation = 'L';
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
