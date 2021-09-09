<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\CarrierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function historical()
    {
        return view('subdomains.carriers.reports.historical');
    }

    public function historicalData(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        return CarrierPayment::whereBetween('date', [$start, $end])
            ->select([
                "carrier_payments.id",
                "carrier_payments.carrier_id",
                "carrier_payments.date",
                "carrier_payments.gross_amount",
                "carrier_payments.reductions",
                "carrier_payments.total",
                "carrier_payments.status",
            ])
            ->where('carrier_id', auth()->user()->id)
            ->where('status', 'completed')
            ->get();
    }
}
