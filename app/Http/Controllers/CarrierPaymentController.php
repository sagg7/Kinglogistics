<?php

namespace App\Http\Controllers;

use App\Enums\CarrierPaymentEnum;
use App\Mail\SendCarrierPayments;
use App\Models\Carrier;
use App\Models\CarrierPayment;
use App\Models\CarrierExpense;
use App\Traits\Accounting\CarrierPaymentsPDF;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class CarrierPaymentController extends Controller
{
    use GetSimpleSearchData, CarrierPaymentsPDF;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('carrierPayments.index');
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
     * @param  \App\Models\CarrierPayment  $carrierPayment
     * @return \Illuminate\Http\Response
     */
    public function show(CarrierPayment $carrierPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CarrierPayment  $carrierPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(CarrierPayment $carrierPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarrierPayment  $carrierPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarrierPayment $carrierPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CarrierPayment  $carrierPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarrierPayment $carrierPayment)
    {
        //
    }

    public function approve($id)
    {
        $payment = CarrierPayment::findOrFail($id);
        $payment->status = CarrierPaymentEnum::APPROVED;

        return ['success' => $payment->save()];
    }

    public function complete($id)
    {
        $payment = CarrierPayment::with('carrier:id,invoice_email,name')->findOrFail($id);

        $emails = explode(',', $payment->carrier->invoice_email);
        try {
            $pdf = $this->getPDFBinary($payment->id);
            foreach ($emails as $email) {
                Mail::to($email)->send(new SendCarrierPayments($payment->carrier, $pdf));
            }
        } catch (MpdfException $e) {
        }
        $payment->status = CarrierPaymentEnum::COMPLETED;
        $payment->save();

        return ['success' => $payment->save()];
    }

    public function payCharges($carrier_id)
    {
        return DB::transaction(function () use ($carrier_id) {
            $expenses = CarrierExpense::where('carrier_id', $carrier_id)
                ->where('non_editable', 1)
                ->whereNull('carrier_payment_id')
                ->get();

            $carrier_payment = new CarrierPayment();
            $carrier_payment->date = Carbon::now();
            $carrier_payment->carrier_id = $carrier_id;
            $carrier_payment->status = 'charges';
            $carrier_payment->save();

            $reductions = 0;

            foreach ($expenses as $expense) {
                $reductions += $expense->amount;
                $expense->carrier_payment_id = $carrier_payment->id;
                $expense->save();
            }

            $carrier_payment->reductions = $reductions;
            $carrier_payment->total = -$reductions;
            $carrier_payment->save();

            return ['success' => true];
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        switch ($type) {
            default:
            case 'pending':
            case 'completed':
            case 'completedCharges':
                return $this->searchCarrierPayments($request, $type);
            case 'pendingCharges':
                return $this->searchCarrierExpenses($request);
        }
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'carrier':
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
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    private function searchCarrierPayments(Request $request, $type)
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
            ->with('carrier:id,name')
            ->where(function ($q) use ($type) {
                switch ($type) {
                    case 'pending':
                        $q->where('status', 'pending')
                            ->orWhere('status', 'approved');
                        break;
                    case 'completed':
                        $q->where('status', 'completed');
                        break;
                    case 'completedCharges':
                        $q->where('status', 'charges');
                        break;
                }
            });

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function searchCarrierExpenses(Request $request)
    {
        $query = Carrier::select([
            "carriers.id",
            "carriers.name",
        ])
            ->whereHas('expenses', function ($q) {
                $q->where('non_editable', 1)
                    ->whereNull('carrier_payment_id');
            })
            ->with('expenses', function ($q) {
                $q->where('non_editable', 1)
                    ->whereNull('carrier_payment_id');
            });

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
