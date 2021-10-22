<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diesel extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}
