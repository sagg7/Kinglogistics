<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasFactory, SoftDeletes;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return HasMany
     */
    public function driver(): HasMany
    {
        return $this->HasMany(Driver::class);
    }

    /**
     * @return BelongsTo
     */
    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

}
