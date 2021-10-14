<?php

namespace App\Traits\Turn;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DriverTurn
{
    private function filterByActiveTurn($query)
    {
        $now = Carbon::now();
        $timeString = $now->toTimeString();
        $query->where(function ($q) use ($timeString, $now) {
            // Logic for night shift
            $q->whereTime('turns.end', '<', DB::raw('TIME(turns.start)'));
            if ($now->hour >= 0 && $now->hour <= 12)
                $q->whereTime('turns.end', '>', $timeString);
            else
                $q->whereTime('turns.start', '<=', $timeString);
        })
            // Logic for morning shift
            ->orWhere(function ($q) use ($timeString) {
                $q->whereTime('turns.end', '>', DB::raw('TIME(turns.start)'))
                    ->whereTime('turns.start', '<=', $timeString)
                    ->whereTime('turns.end', '>', $timeString);
            });
    }

    private function filterByInactiveTurn($query)
    {
        $now = Carbon::now();
        $timeString = $now->toTimeString();
        $query->where(function ($q) use ($timeString, $now) {
            // Logic for night shift
            $q->whereTime('turns.end', '<', DB::raw('TIME(turns.start)'));
            if ($now->hour >= 0 && $now->hour <= 12)
                $q->whereTime('turns.end', '<=', $timeString);
            else
                $q->whereTime('turns.start', '>', $timeString);
        })
            // Logic for morning shift
            ->orWhere(function ($q) use ($timeString) {
                $q->whereTime('turns.end', '>', DB::raw('TIME(turns.start)'))
                    ->whereTime('turns.start', '>', $timeString)
                    ->whereTime('turns.end', '<=', $timeString);
            });
    }
}
