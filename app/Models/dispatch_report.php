<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dispatch_report extends Model
{
    use HasFactory;
    
    public function dispatch()
    {
        return $this->belongsTo(User::class, 'dispatch_id', 'id');
    }
}
