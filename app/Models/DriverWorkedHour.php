<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverWorkedHour extends Model
{
    use HasFactory;

    protected $casts = [
        'shift_start' => 'timestamp',
        'shift_end' => 'timestamp',
    ];

    protected $fillable = [
        'driver_id',
        'worked_hours',
        'shift_start',
        'shift_end',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
