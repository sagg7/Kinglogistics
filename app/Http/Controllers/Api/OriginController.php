<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Origin;
use Illuminate\Http\Request;

class OriginController extends Controller
{
    public function getOrigins()
    {
        $origins = Origin::select([
            'id as key',
            'name as value',
        ])
            ->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found origins',
            'origins' => KeyValueResource::collection($origins),
        ]);
    }
}
