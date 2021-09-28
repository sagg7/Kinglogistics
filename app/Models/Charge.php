<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Charge extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @return BelongsToMany
     */
    public function carriers(): BelongsToMany
    {
        return $this->belongsToMany(Carrier::class);
    }

    /**
     * @return BelongsTo
     */
    public function charge_type(): BelongsTo
    {
        return $this->belongsTo(ChargeType::class);
    }
}
