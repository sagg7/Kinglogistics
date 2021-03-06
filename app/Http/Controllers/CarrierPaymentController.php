<?php

namespace App\Http\Controllers;

use App\Enums\CarrierPaymentEnum;
use App\Exports\CarrierPaymentExport;
use App\Exports\TemplateExport;
use App\Mail\SendCarrierPayments;
use App\Models\Bonus;
use App\Models\BonusType;
use App\Models\Carrier;
use App\Models\CarrierExpenseType;
use App\Models\CarrierPayment;
use App\Models\CarrierExpense;
use App\Traits\Accounting\CarrierPaymentsPDF;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
     * @return array
     */
    private function createEditParams(): array
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $carrierPayment = CarrierPayment::with([
            'bonuses.bonus_type',
            'expenses.type',
            'carrier:id,name',
        ])
            ->whereHas('carrier', function ($q) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            })
            ->findOrFail($id);
        $bonusTypes = BonusType::pluck('name', 'id')->toArray();
        $expenseTypes = CarrierExpenseType::whereNull('carrier_id')->pluck('name', 'id')->toArray();
        $params = compact('carrierPayment', 'bonusTypes', 'expenseTypes');
        return view('carrierPayments.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarrierPayment  $carrierPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $carrierPayment = CarrierPayment::with([
                'bonuses',
                'expenses',
            ])
                ->whereHas('carrier', function ($q) {
                    $q->whereHas('broker', function ($q) {
                        $q->where('id', session('broker'));
                    });
                })
                ->findOrFail($id);

            $carrier_id = $carrierPayment->carrier_id;

            if ($request->bonuses)
                foreach ($request->bonuses as $item) {
                    if (!is_numeric($item["id"])) {
                        // In case of adding new bonus
                        $bonus = new Bonus();
                        $bonus->bonus_type_id = $item["objType"];
                        $bonus->amount = $item["amount"];
                        $bonus->description = $item["description"];
                        $bonus->date = Carbon::createFromFormat('m/d/Y', $item["date"]);
                        $bonus->save();
                        $bonus->carriers()->sync([$carrier_id => ['carrier_payment_id' => $carrierPayment->id]]);
                    } else if (isset($item["delete"])) {
                        // In case of removing bonus
                        $bonus = Bonus::find($item["id"]);
                        $bonus->carriers()->detach($carrier_id);
                        $bonus->carriers()->count() > 0 ?: $bonus->delete();
                    }
                }

            if ($request->expenses)
                foreach ($request->expenses as $item) {
                    if (!is_numeric($item["id"])) {
                        // In case of adding new expense
                        $expense = new CarrierExpense();
                        $expense->type_id = $item["objType"];
                        $expense->amount = $item["amount"];
                        $expense->description = $item["description"];
                        $expense->date = Carbon::createFromFormat('m/d/Y', $item["date"]);
                        $expense->carrier_id = $carrier_id;
                        $expense->carrier_payment_id = $carrierPayment->id;
                        $expense->non_editable = true;
                        $expense->save();
                    } else if (isset($item["delete"])) {
                        // In case of removing expense
                        $expense = CarrierExpense::find($item["id"]);
                        $expense->delete();
                    }
                }

            $carrierPayment->gross_amount = $request->subtotal;
            $carrierPayment->reductions = $request->reductions;
            $carrierPayment->total = $request->total;
            $carrierPayment->save();

            return ['success' => true];
        });
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
        $payment = CarrierPayment::whereHas('carrier', function ($q) {
            $q->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        })
            ->findOrFail($id);
        $payment->status = CarrierPaymentEnum::APPROVED;

        return ['success' => $payment->save()];
    }

    public function complete($id)
    {
        $payment = CarrierPayment::whereHas('carrier', function ($q) {
            $q->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        })
            ->with('carrier:id,invoice_email,name')->findOrFail($id);

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
            $carrier_payment->status = CarrierPaymentEnum::CHARGES;
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
    public function search(Request $request, $type, $download = false)
    {
        switch ($type) {
            default:
            case CarrierPaymentEnum::PENDING:
            case CarrierPaymentEnum::COMPLETED:
            case CarrierPaymentEnum::DAILY:
            case 'completedCharges':
                return $this->searchCarrierPayments($request, $type, $download);
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
     * @return array|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
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
            ->whereHas('carrier', function ($q) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            })
            ->with('carrier:id,name')
            ->where(function ($q) use ($type) {
                switch ($type) {
                    case CarrierPaymentEnum::PENDING:
                        $q->where('status', CarrierPaymentEnum::PENDING)
                            ->orWhere('status', CarrierPaymentEnum::APPROVED);
                        break;
                    case CarrierPaymentEnum::COMPLETED:
                        $q->where('status', CarrierPaymentEnum::COMPLETED);
                        break;
                    case CarrierPaymentEnum::DAILY:
                        $q->where('status', CarrierPaymentEnum::DAILY);
                        break;
                    case 'completedCharges':
                        $q->where('status', CarrierPaymentEnum::CHARGES);
                        break;
                }
            });

        if ($request->download) {
            $query = $query->get();
            if (count($query) === 0)
                return redirect()->back()->withErrors('There are no payments to generate the document');
            $data = [];
            foreach ($query as $item) {
                $data[] = [
                    $item->date->format('m/d/Y'),
                    $item->carrier->name,
                    $item->gross_amount,
                    $item->reductions,
                    $item->total,
                    ucfirst($item->status),
                ];
            }
            return (new TemplateExport([
                "data" => $data,
                "headers" => ["Date", "Carrier", "Subtotal", "Reductions", "Total", "Status"],
                "formats" => [
                    'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                ],
            ]))->download("Rentals " . ucfirst($type) . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
        }

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
            ->whereHas('carrier', function ($q) {
                $q->whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                });
            })
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
     */
    public function downloadPDF($id)
    {
        return $this->generatePDF($id)->Output();
    }

    public function downloadXLSX($id)
    {
        return (new CarrierPaymentExport($id))->download();
    }

    public function pending($id){
        $carrierPayment = CarrierPayment::findOrFail($id);
        $carrierPayment->status = "pending";
        if ($carrierPayment->save()) {
            return ['success' => true, 'user' => $carrierPayment];
        } else
            return ['success' => false];
    }
}
