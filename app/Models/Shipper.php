<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shipper extends Authenticatable
{
    use HasFactory, SoftDeletes;

    public function getPaymentDaysAttribute($value)
    {
        return explode(',', $value);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function loads()
    {
        return $this->hasMany(Load::class);
    }

    /**
     * @return BelongsToMany
     */
    public function trailers(): BelongsToMany
    {
        return $this->belongsToMany(Trailer::class);
    }

    public function locationGroup(): HasOne
    {
        return $this->hasOne(LocationGroup::class);
    }
}
