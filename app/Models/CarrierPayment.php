<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierPayment extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
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
    public function loads(): HasMany
    {
        return $this->hasMany(Load::class);
    }
}
