<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes, S3Functions;

    protected $fillable = [
        'content',
        'driver_id',
        'user_id',
        'is_driver_sender',
        'user_unread',
        'driver_unread',
        'image',
    ];

    protected $casts = [
        'is_driver_sender' => 'boolean'
    ];

    protected $appends = [
        'image_url'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
     * Mutators
     */

    public function getImageUrlAttribute(): ?string
    {
        return !empty($this->image) ? $this->getTemporaryFile($this->image) : null;
    }
}
