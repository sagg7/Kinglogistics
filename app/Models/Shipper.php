<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shipper extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * @return BelongsToMany
     */
    public function trailers(): BelongsToMany
    {
        return $this->belongsToMany(Trailer::class);
    }
}
