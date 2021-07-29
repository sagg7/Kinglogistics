<?php

namespace App\Http\Controllers;

use App\Models\ShipperInvoice;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ShipperInvoiceController extends Controller
{
    use GetSimpleSearchData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shipperInvoices.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShipperInvoice  $shipperInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(ShipperInvoice $shipperInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShipperInvoice  $shipperInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(ShipperInvoice $shipperInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShipperInvoice  $shipperInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShipperInvoice $shipperInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShipperInvoice  $shipperInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShipperInvoice $shipperInvoice)
    {
        //
    }

    public function complete($id)
    {
        $invoice = ShipperInvoice::findOrFail($id);
        $invoice->status = 'completed';

        return ['success' => $invoice->save()];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = ShipperInvoice::select([
            "shipper_invoices.id",
            "shipper_invoices.shipper_id",
            "shipper_invoices.date",
            "shipper_invoices.total",
            "shipper_invoices.status",
        ])
            ->with('shipper:id,name')
            ->where(function ($q) use ($type) {
                switch ($type) {
                    case 'pending':
                    case 'completed':
                        $q->where('status', $type);
                        break;
                    default:
                        abort(404);
                }
            });

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'shipper':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%$request->search%");
                        });
                        $statement = "orWhereHas";
                        break;
                    default:
                        $searchable[count($searchable) + 1] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->simpleSearchData($query, $request, 'orWhere');
    }

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function downloadPDF($id)
    {
        $shipperInvoice = ShipperInvoice::with([
            'shipper:id,name',
            'loads.driver.truck',
        ])
            ->findOrFail($id);

        $mpdf = new Mpdf();
        $mpdf->SetHTMLHeader('<div style="text-align: left; font-weight: bold;"><img style="width: 160px;" src=' . asset('images/logo.png') . ' alt="Logo"></div>');

        $title = "Shipper Invoice - " . $shipperInvoice->date->format('m/d/Y');
        $html = view('exports.shipperInvoices.pdf', compact('title', 'shipperInvoice'));
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
        return $mpdf->Output();
    }
}
