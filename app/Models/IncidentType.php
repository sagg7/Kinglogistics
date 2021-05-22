<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentType extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
