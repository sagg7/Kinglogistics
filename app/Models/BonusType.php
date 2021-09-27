<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusType extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function bonuses(): HasMany
    {
        return $this->hasMany(BOnus::class);
    }
}