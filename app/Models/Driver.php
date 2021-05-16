<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    /**
     * @return HasOne
     */
    public function carrier(): HasOne
    {
        return $this->hasOne(Carrier::class, 'id', 'carrier_id');
    }

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'patient_id');
    }
}
