<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Trailer;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function getTrailers()
    {
        $trailers = Trailer::select([
            'id as key',
            'number as value',
        ])
            ->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found trailers',
            'trailers' => KeyValueResource::collection($trailers),
        ]);
    }
}
