<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
            return $this->belongsTo(ExpenseAccount::class)
            ->withTrashed();
    }
}
