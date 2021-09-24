<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    use GetSimpleSearchData;

    public function index()
    {
        return view('subdomains.carriers.accounting.loans.index');
    }

    public function search(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $query = Loan::select([
            "loans.id",
            "loans.amount",
            "loans.paid_amount",
            "loans.installments",
            "loans.paid_installments",
            "loans.fee_percentage",
            "loans.carrier_id",
            "loans.date",
            "loans.is_paid",
        ])
            ->whereBetween('date', [$start, $end])
            ->whereHas('carrier', function ($q) {
                $q->where('carrier_id', auth()->user()->id);
            });

        return $this->multiTabSearchData($query, $request);
    }
}
