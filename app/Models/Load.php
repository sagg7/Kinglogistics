<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Load extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo
     */
    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    /**
     * @return BelongsTo
     */
    public function load_type(): BelongsTo
    {
        return $this->belongsTo(LoadType::class);
    }
}
