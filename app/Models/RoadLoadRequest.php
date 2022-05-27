<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RoadLoadRequest extends Model
{
    use HasFactory;

    public function requestable(): MorphTo
    {
        return $this->morphTo();
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(RoadLoad::class, 'road_load_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
