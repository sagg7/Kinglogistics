<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trailer extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'patient_id');
    }

    public function trailer_type(): HasOne
    {
        return $this->hasOne(TrailerType::class, 'id', 'trailer_type_id');
    }

    /**
     * @return HasOne
     */
    public function truck(): HasOne
    {
        return $this->HasOne(Truck::class);
    }
}
