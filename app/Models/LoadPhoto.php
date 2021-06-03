<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadPhoto extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function getLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }
}
