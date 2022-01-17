<?php

namespace App\Traits\Accounting;

use App\Models\Broker;
use App\Models\ShipperInvoice;
use App\Traits\Storage\S3Functions;
use Mpdf\Mpdf;

trait ShipperInvoicesPDF
{
    use S3Functions;

    private function generatePDF($id)
    {
        $shipperInvoice = ShipperInvoice::with([
            'shipper:id,name',
            'loads.driver.truck',
        ])
            ->whereHas('shipper', function ($q) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            })
            ->findOrFail($id);

        $broker = Broker::findOrFail(session('broker'));

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

    private function generatePicturesPdf($id)
    {
        $shipperInvoice = ShipperInvoice::with([
            'shipper:id,name',
            'loads.loadStatus',
        ])
            ->findOrFail($id);

        $broker = Broker::findOrFail(session('broker'));
        $photos = [];

        foreach ($shipperInvoice->loads as $load){
            if(isset($load->loadStatus)){
                $photos[] = $this->getTemporaryFile($load->loadStatus->finished_voucher);
                $photos[] = $this->getTemporaryFile($load->loadStatus->to_location_voucher);
            }
        }
        $mpdf = new Mpdf();
        //$mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/app/logos/logo.png') . ' alt="Logo"></div>');

        $title = "Shipper Invoice - " . $shipperInvoice->shipper->name ."  - " . $shipperInvoice->date->format('m/d/Y');
        $html = view('exports.shipperInvoices.invoicePictures', compact('title', 'shipperInvoice', 'broker', 'photos'));
        $orientation = 'P';
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

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    private function getStatementPhotos($id)
    {
        return $this->generatePicturesPdf($id)->Output('', 'S');
    }
}
