<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RejectedLoad extends Model
{
    use HasFactory;

    public $fillable = [
        'load_id',
        'driver_id',
    ];

    /**
     * Establish a relationship with the Load model, has to be named like "parentLoad" due overlapping "load()" Laravel method.
     *
     * @return BelongsTo
     */

    public function parentLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
