<?php

namespace App\Traits\Timezone;

use App\Models\Timezone;
use Illuminate\Support\Facades\DB;

trait TimezoneParams
{
    private function getTimezoneSelection()
    {
        return [null => 'Select'] + Timezone::select([
                'id',
                DB::raw('CONCAT("(", abbreviation, ") ", name) AS text')
            ])
                ->pluck('text', 'id')->toArray();
    }
}
