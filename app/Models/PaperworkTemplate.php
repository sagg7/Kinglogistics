<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperworkTemplate extends Model
{
    use HasFactory;

    protected $casts = [
        'filled_template' => 'array'
    ];
}
