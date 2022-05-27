<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadLoad extends Model
{
    use HasFactory;

    public function parentLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class, 'load_id');
    }

    /**
     * @return BelongsTo
     */
    public function mode(): BelongsTo
    {
        return $this->belongsTo(LoadMode::class);
    }

    /**
     * @return BelongsTo
     */
    public function trailer_type(): BelongsTo
    {
        return $this->belongsTo(LoadTrailerType::class);
    }

    /**
     * @return BelongsTo
     */
    public function origin_city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    /**
     * @return BelongsTo
     */
    public function destination_city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function request()
    {
        return $this->hasOne(RoadLoadRequest::class);
    }
}
