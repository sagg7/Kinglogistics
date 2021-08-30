<?php

namespace App\Traits\Turn;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DriverTurn
{
    private function filterByActiveTurn($query, $inactive = false)
    {
        $now = Carbon::now();
        $timeString = $now->toTimeString();
        $query->where(function ($q) use ($timeString, $now) {
            $q->whereTime('turns.end', '<', DB::raw('TIME(turns.start)'));
            if ($now->hour >= 0 && $now->hour <= 12)
                $q->whereTime('end', '>', $timeString);
            else
                $q->whereTime('start', '<=', $timeString);
        })
            ->orWhere(function ($q) use ($timeString) {
                $q->whereTime('turns.end', '>', DB::raw('TIME(turns.start)'))
                    ->whereTime('start', '<=', $timeString)
                    ->whereTime('end', '>', $timeString);
            });
    }
}
