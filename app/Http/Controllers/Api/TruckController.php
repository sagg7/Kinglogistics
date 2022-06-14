<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function getActiveTruck()
    {
        $driver = auth()->user()->load([
            'truck' => function ($q) {
                $q->select('id', 'driver_id', 'trailer_id', 'number');
            }
        ]);

        return $driver->truck ?? ["message" => "No active truck found"];
    }

    public function getTrucks()
    {
        $trucks = Truck::select([
            'id as key',
            'number as value',
        ])
            ->where('carrier_id', auth()->user()->carrier_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found trucks',
            'trucks' => KeyValueResource::collection($trucks),
        ]);
    }
}
