<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeType extends Model
{
    use HasFactory;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return HasMany
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
