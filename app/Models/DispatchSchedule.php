<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchSchedule extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDispatchInShift($date = null) {
        if ($date == null)
            $date = Carbon::now();
        $dispatch = DispatchSchedule::where('day', $date->dayOfWeek-1)
        ->where('time', $date->format("H").':00:00')->first();
        if ($dispatch)
            return $dispatch->user;
        else
            return null;
    }
}
