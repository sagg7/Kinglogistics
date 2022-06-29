<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Timezone;
use Illuminate\Support\Facades\DB;

class TimezoneController extends Controller
{
    public function getTimezones()
    {
        $timezones = Timezone::select([
            'id',
            DB::raw('CONCAT("(", abbreviation, ") ", name) AS text')
        ])
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found timezones',
            'timezones' => KeyValueResource::collection($timezones),
        ]);
    }
}
