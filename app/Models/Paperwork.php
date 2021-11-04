<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paperwork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "paperwork";

    protected $appends = ['file_name'];

    public function getFileNameAttribute()
    {
        $exploded = explode('/', $this->file);
        return explode('?', $exploded[count($exploded) - 1])[0];
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }
}
