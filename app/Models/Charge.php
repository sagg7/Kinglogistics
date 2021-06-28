<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Charge extends Model
{
    use HasFactory;

    /**
     * @return BelongsToMany
     */
    public function carriers(): BelongsToMany
    {
        return $this->belongsToMany(Carrier::class);
    }
}
