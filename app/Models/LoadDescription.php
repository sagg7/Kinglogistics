<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadDescription extends Model
{
    use HasFactory;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
}
