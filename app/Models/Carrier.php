<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Carrier extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'password'
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
    /**
     * @return HasMany
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
    /**
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(CarrierExpense::class);
    }
    /**
     * @return HasMany
     */
    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class);
    }
    /**
     * @return HasMany
     */
    public function trailers(): HasMany
    {
        return $this->hasMany(Trailer::class);
    }
    /**
     * @return HasMany
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function locationGroup(): HasOne
    {
        return $this->hasOne(LocationGroup::class);
    }

    public function ranking(): HasOne
    {
        return $this->hasOne(CarrierRanking::class);
    }

    /**
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
