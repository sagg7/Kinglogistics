<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierExpense extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'date:m/d/Y',
        'date' => 'date:m/d/Y',
    ];

    protected $fillable = [
        'amount',
        'description',
        'date',
    ];

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
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CarrierExpenseType::class);
    }
}
