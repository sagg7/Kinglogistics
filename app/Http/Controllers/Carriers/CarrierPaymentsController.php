<?php

namespace App\Http\Controllers\Carriers;

use App\Enums\CarrierPaymentEnum;
use App\Http\Controllers\Controller;
use App\Models\CarrierPayment;
use App\Traits\Accounting\CarrierPaymentsPDF;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;

class CarrierPaymentsController extends Controller
{
    use GetSimpleSearchData, CarrierPaymentsPDF;

    public function index()
    {
        return view('subdomains.carriers.accounting.payments.index');
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = CarrierPayment::select([
            "carrier_payments.id",
            "carrier_payments.carrier_id",
            "carrier_payments.date",
            "carrier_payments.gross_amount",
            "carrier_payments.reductions",
            "carrier_payments.total",
            "carrier_payments.status",
        ])
            ->where('carrier_id', auth()->user()->id)
            ->where('status', CarrierPaymentEnum::COMPLETED);

        return $this->multiTabSearchData($query, $request);
    }

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function downloadPDF($id)
    {
        $mpdf = $this->generatePDF($id);
        return $mpdf->Output();
    }
}
