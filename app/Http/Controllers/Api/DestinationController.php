<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function getDestinations()
    {
        $destinations = Destination::select([
            'id as key',
            'name as value',
        ])
            ->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found destinations',
            'destinations' => KeyValueResource::collection($destinations),
        ]);
    }
}
