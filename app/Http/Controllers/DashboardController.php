<?php

namespace App\Http\Controllers;

use App\Models\Load;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getData(Request $request)
    {
        $start = Carbon::now()->subMonths(3)->startOfMonth();
        $end = Carbon::now()->endOfMonth()->endOfDay();
        $loads = Load::whereBetween('loads.date', [$start, $end])
            ->get();

        $loadsSummary = [];
        foreach ($loads as $load) {
            if (isset($loadsSummary[$load->status]))
                $loadsSummary[$load->status]++;
            else
                $loadsSummary[$load->status] = 1;
        }

        return [
            'loads' => $loadsSummary
        ];
    }
}
