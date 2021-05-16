<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Truck extends Model
{
    use HasFactory;

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }
}
