<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarrierExpenseType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(CarrierExpense::class);
    }
}
