<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\CarrierPayment;
use App\Models\CarrierExpense;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class CarrierPaymentController extends Controller
{
    use GetSimpleSearchData;
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

    public function complete($id)
    {
        $payment = CarrierPayment::findOrFail($id);
        $payment->status = 'completed';

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
                        $q->where('status', 'pending');
                        break;
                    case 'completed':
                        $q->where('status', 'completed');
                        break;
                    case 'completedCharges':
                        $q->where('status', 'charges');
                        break;
                }
            });

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carrier':
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

        return $this->simpleSearchData($query, $request);
    }

    /**
     * @param $id
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function downloadPDF($id)
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
        return $mpdf->Output();
    }
}
