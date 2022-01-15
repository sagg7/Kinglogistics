<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bonus extends Model
{
    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    protected $fillable = [
        'amount',
        'description',
        'date',
    ];

    use HasFactory;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return BelongsToMany
     */
    public function carriers(): BelongsToMany
    {
        return $this->belongsToMany(Carrier::class)->withPivot(['carrier_payment_id']);
    }

    /**
     * @return BelongsTo
     */
    public function bonus_type(): BelongsTo
    {
        return $this->belongsTo(BonusType::class);
    }
}
