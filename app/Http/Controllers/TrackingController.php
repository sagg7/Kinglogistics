<?php

namespace App\Http\Controllers;

use App\Models\Load;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        $loads = Load::whereHas('latestLocation')
            ->with([
                'latestLocation' => function ($q) {
                    $q->with([
                        'driver' => function ($q) {
                            $q->with([
                                'carrier:id,name',
                            ])
                                ->select([
                                    'drivers.id',
                                    'drivers.name',
                                    'drivers.carrier_id',
                                ]);
                        },
                    ]);
                },
                'truck:id,number',
                'shipper:id,name',
            ])
            ->get([
                'loads.id',
                'loads.origin',
                'loads.destination',
                'loads.driver_id',
                'loads.truck_id',
                'loads.shipper_id',
            ]);

        $params = compact('loads');

        return view('loads.tracking', $params);
    }
}
