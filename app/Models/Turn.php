<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turn extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    /**
     * @return HasOne
     */
    public function zone(): HasOne
    {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }
}
