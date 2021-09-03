<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Trailer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function trailers()
    {
        return view('subdomains.shippers.reports.trailers');
    }

    public function trailersData()
    {
        return Trailer::whereHas('shippers', function ($q) {
            $q->where('id', auth()->user()->id);
        })
            /*->whereHas('truck.driver', function ($q) {
                $q->whereHas('latestLoad', function ($q) {
                    $q->where('box_status_end', "loaded");
                });
            })*/
            ->with([
                'truck' => function ($q) {
                    $q->with(['driver' => function ($q) {
                        $q->with(['latestLoad' => function ($q) {
                            $q->with('boxEnd:id,name')
                                ->select([
                                    'id',
                                    'driver_id',
                                    'box_type_id_end',
                                    'box_number_end',
                                    'box_status_end',
                                ]);
                        }])
                            ->select([
                                'drivers.id',
                                'drivers.name',
                            ]);
                    }])
                        ->select([
                            'trucks.id',
                            'trucks.trailer_id',
                            'trucks.driver_id',
                            'trucks.number',
                        ]);
                },
                'chassisType:id,name',
            ])
            ->get();
    }
}
