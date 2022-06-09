<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use Illuminate\Http\Request;

class ShipperController extends Controller
{
    public function getShippers()
    {
        $shippers = auth()->user()->shippers()->select([
            'id as key',
            'name as value',
        ])->get();

        return response([
            'status' => 'ok',
            'message' => 'Found shippers',
            'shippers' => KeyValueResource::collection($shippers),
        ]);
    }
}
