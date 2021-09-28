<?php

namespace App\Http\Controllers;

use App\Enums\ShipperInvoiceEnum;
use App\Exports\ShipperInvoiceExport;
use App\Mail\SendShipperInvoices;
use App\Models\ShipperInvoice;
use App\Traits\Accounting\ShipperInvoicesPDF;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;

class ShipperInvoiceController extends Controller
{
    use GetSimpleSearchData, ShipperInvoicesPDF;

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
        $payment = ShipperInvoice::with('shipper:id,invoice_email,name')
            ->where('status', ShipperInvoiceEnum::PENDING)
            ->findOrFail($id);

        $emails = explode(',', $payment->shipper->invoice_email);
        try {
            $xlsx = Excel::raw(new ShipperInvoiceExport($payment->id), \Maatwebsite\Excel\Excel::XLSX);
            $pdf = Excel::raw(new ShipperInvoiceExport($id), \Maatwebsite\Excel\Excel::MPDF);
            foreach ($emails as $email) {
                Mail::to($email)->send(new SendShipperInvoices($payment->shipper, $xlsx, $pdf));
            }
        } catch (MpdfException $e) {
        }
        $payment->status = ShipperInvoiceEnum::COMPLETED;
        $payment->save();

        return ['success' => $payment->save()];
    }

    public function completeAll()
    {
        $invoices = ShipperInvoice::with('shipper:id,invoice_email,name')
            ->where('status', ShipperInvoiceEnum::PENDING)
            ->get();
        foreach ($invoices as $item) {
            if ($item->shipper->invoice_email) {
                $emails = explode(',', $item->shipper->invoice_email);
                try {
                    $xlsx = Excel::raw(new ShipperInvoiceExport($item->id), \Maatwebsite\Excel\Excel::XLSX);
                    $pdf = Excel::raw(new ShipperInvoiceExport($item->id), \Maatwebsite\Excel\Excel::MPDF);
                    foreach ($emails as $email) {
                        Mail::to($email)->send(new SendShipperInvoices($item->shipper, $xlsx, $pdf));
                    }
                } catch (MpdfException $e) {
                    continue;
                }
            }
            $item->status = ShipperInvoiceEnum::COMPLETED;
            $item->save();
        }
        return ['success' => true];
    }

    public function pending($id)
    {
        // Return invoice from completed to pending
        $payment = ShipperInvoice::with('shipper:id,invoice_email,name')
            ->where('status', ShipperInvoiceEnum::COMPLETED)
            ->findOrFail($id);

        $payment->status = ShipperInvoiceEnum::PENDING;
        $payment->save();

        return ['success' => $payment->save()];
    }

    public function pay($id)
    {
        // Invoice status from completed to paid
        $payment = ShipperInvoice::with('shipper:id,invoice_email,name')
            ->where('status', ShipperInvoiceEnum::COMPLETED)
            ->findOrFail($id);

        $payment->status = ShipperInvoiceEnum::PAID;
        $payment->save();

        return ['success' => $payment->save()];
    }

    public function payAll()
    {
        ShipperInvoice::where('status', ShipperInvoiceEnum::COMPLETED)
            ->update(['status' => ShipperInvoiceEnum::PAID]);

        return ['success' => true];
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'shipper':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
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
                    case ShipperInvoiceEnum::PENDING:
                    case ShipperInvoiceEnum::COMPLETED:
                    case ShipperInvoiceEnum::PAID:
                        $q->where('status', $type);
                        break;
                    default:
                        abort(404);
                }
            });

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function downloadPDF($id)
    {
        return (new ShipperInvoiceExport($id, \Maatwebsite\Excel\Excel::MPDF))->download();
    }

    public function downloadXLSX($id)
    {
        return (new ShipperInvoiceExport($id))->download();
    }
}
