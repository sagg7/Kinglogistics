<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'driver_id',
        'messageable_id',
        'messageable_type',
        'is_driver_sender',
    ];

    protected $casts = [
        'is_driver_sender' => 'boolean'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }
}
