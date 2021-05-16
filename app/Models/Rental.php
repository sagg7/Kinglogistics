<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @return HasOne
     */
    public function carrier(): HasOne
    {
        return $this->hasOne(Carrier::class, 'id', 'carrier_id');
    }

    /**
     * @return HasOne
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    /**
     * @return HasOne
     */
    public function trailer(): HasOne
    {
        return $this->hasOne(Trailer::class, 'id', 'trailer_id');
    }
}
