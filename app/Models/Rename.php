<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rename extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

}
