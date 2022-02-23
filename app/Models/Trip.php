<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return HasMany
     */
    public function loads(): HasMany
    {
        return $this->hasMany(Load::class);
    }

    /**
     * @return BelongsTo
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * @return BelongsTo
     */
    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    /**
     * @return BelongsTo
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function trip_origin(): BelongsTo
    {
        return $this->belongsTo(Origin::class, 'origin_id')
            ->withTrashed();
    }

    public function trip_destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id')
            ->withTrashed();
    }
}
