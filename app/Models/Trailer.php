<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trailer extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['shippers_ids'];

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * @return BelongsTo
     */
    public function trailer_type(): BelongsTo
    {
        return $this->belongsTo(TrailerType::class);
    }

    /**
     * @return BelongsTo
     */
    /*public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }*/

    /**
     * @return HasOne
     */
    public function truck(): HasOne
    {
        return $this->HasOne(Truck::class);
    }

    /**
     * @return BelongsToMany
     */
    public function shippers(): BelongsToMany
    {
        return $this->belongsToMany(Shipper::class);
    }

    public function chassisType(): BelongsTo
    {
        return $this->belongsTo(ChassisType::class);
    }

    public function getShippersIdsAttribute()
    {
        return $this->shippers->pluck('id');
    }
}
