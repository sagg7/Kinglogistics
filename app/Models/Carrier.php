<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Carrier extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'password'
    ];

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
    /**
     * @return HasMany
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
    /**
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
