<?php

namespace App\Http\Controllers\Carriers;

use App\Enums\CarrierPaymentEnum;
use App\Enums\LoadStatusEnum;
use App\Enums\RoleSlugs;
use App\Exports\CarrierPaymentExport;
use App\Http\Controllers\Controller;
use App\Mail\SendCarrierPayments;
use App\Models\CarrierPayment;
use App\Models\Load;
use App\Models\User;
use App\Traits\Accounting\CarrierPaymentsPDF;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\RecalculateTotals;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\MpdfException;

class DailyPayController extends Controller
{
    use RecalculateTotals, GetSimpleSearchData, CarrierPaymentsPDF;

    private function validator(array $data)
    {
        return Validator::make($data, [
            'loads' => ['required', 'array', 'exists:loads,id'],
        ],
        [
            'loads.required' => 'At least one load must be selected',
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     */
    private function storeUpdate(Request $request, int $id = null)
    {
        $this->validator($request->all())->validate();

        DB::transaction(function () use ($request, $id) {
            if ($id) {
                $carrierPayment = CarrierPayment::with('loads')
                    ->where('carrier_id', auth()->user()->id)
                    ->findOrFail($id);
                $loads = $carrierPayment->loads->toArray();
                foreach ($loads as $load) {
                    $index = array_search($load['id'], $request->loads);
                    if ($index === false) {
                        $load->carrier_payment_id = null;
                        $load->save();
                    }
                }
            } else {
                $carrierPayment = new CarrierPayment();
                $carrierPayment->carrier_id = auth()->user()->id;
                $carrierPayment->date = Carbon::now();
                $carrierPayment->status = CarrierPaymentEnum::DAILY;
                $carrierPayment->save();
            }

            $loads = $request->loads
                ? Load::whereIn('id', $request->loads)
                    //->whereNull('carrier_payment_id')
                    ->whereHas('driver', function ($q) {
                        $q->whereHas('carrier', function ($q) {
                            $q->where('id', auth()->user()->id);
                        });
                    })
                    ->where(function ($q) {
                        $q->whereHas('carrier_payment', function ($q) {
                            $q->where('status', CarrierPaymentEnum::PENDING)
                                ->orWhere('status', CarrierPaymentEnum::APPROVED)
                                ->orWhere('status', CarrierPaymentEnum::DAILY);
                        })
                            ->orWhereNull('carrier_payment_id');
                    })
                    ->with('carrier_payment')
                    ->get()
                : [];

            foreach ($loads as $load) {
                $pastCarrierPayment = $load->carrier_payment;

                $load->carrier_payment_id = $carrierPayment->id;
                $load->save();

                if ($pastCarrierPayment)
                    $this->recalculateCarrierPayment($pastCarrierPayment);
            }

            $this->recalculateCarrierPayment($carrierPayment);

            if (!$id) {
                $accountantDirectors = User::whereHas('roles', function ($q) {
                    $q->where('slug', RoleSlugs::ACCOUNTANT_DIRECTOR);
                })
                    ->get();
                try {
                    $pdf = $this->getPDFBinary($carrierPayment->id);
                    foreach ($accountantDirectors as $item) {
                        Mail::to($item->email)->send(new SendCarrierPayments($carrierPayment->carrier, $pdf, "Daily Pay Request"));
                    }
                } catch (MpdfException $e) {
                }
            }
        });

        return view('subdomains.carriers.accounting.dailyPay.index');
    }

    public function index()
    {
        return view('subdomains.carriers.accounting.dailyPay.index');
    }

    private function getPendingLoads()
    {
        return Load::whereHas('driver', function ($q) {
            $q->whereHas('carrier', function ($q) {
                $q->where('id', auth()->user()->id);
            });
        })
            ->where(function ($q) {
                $q->whereHas('carrier_payment', function ($q) {
                    $q->where('status', CarrierPaymentEnum::PENDING)
                        ->orWhere('status', CarrierPaymentEnum::APPROVED);
                })
                    ->orWhereNull('carrier_payment_id');
            })
            ->where('status', LoadStatusEnum::FINISHED)
            ->with('driver:id,name')
            ->get()->toArray();
    }

    public function create()
    {
        $loads = $this->getPendingLoads();
        $params = compact('loads');
        return view('subdomains.carriers.accounting.dailyPay.create', $params);
    }

    public function store(Request $request)
    {
        return $this->storeUpdate($request);
    }

    public function edit($id)
    {
        $carrierPayment = CarrierPayment::with([
            'loads' => function ($q) {
                $q->with('driver:id,name');
            },
        ])
            ->where('carrier_id', auth()->user()->id)
            ->where('status', CarrierPaymentEnum::DAILY)
            ->findOrFail($id);
        $paymentLoads = [];
        foreach ($carrierPayment->loads as $load) {
            $paymentLoads[] = $load->toArray() + ['checked' => true];
        }
        $loads = $paymentLoads + $this->getPendingLoads();
        $params = compact('carrierPayment', 'loads');
        return view('subdomains.carriers.accounting.dailyPay.edit', $params);
    }

    /**
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, int $id)
    {
        return $this->storeUpdate($request, $id);
    }

    public function destroy(int $id)
    {
        $carrierPayment = CarrierPayment::where('carrier_id', auth()->user()->id)
            ->where('status', CarrierPaymentEnum::DAILY)
            ->findOrFail($id);

        if ($carrierPayment)
            return ['success' => $carrierPayment->delete()];
        else
            return ['success' => false];
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
            ->with('carrier:id,name')
            ->where('status', CarrierPaymentEnum::DAILY);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
