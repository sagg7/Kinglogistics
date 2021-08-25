<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChassisType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function trailers(): HasMany
    {
        return $this->hasMany(Trailer::class);
    }
}
