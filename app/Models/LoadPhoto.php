<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadPhoto extends Model
{
    use HasFactory, S3Functions;

    /**
     * @param $value
     * @return string
     */
    public function getUrlAttribute($value): string
    {
        return $this->getTemporaryFile($value);
    }

    /**
     * @return BelongsTo
     */
    public function getLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }
}
