<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOpportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'message_json' => 'json',
    ];

    /**
     * @return BelongsToMany
     */
    public function carriers(): BelongsToMany
    {
        return $this->belongsToMany(Carrier::class);
    }
}
