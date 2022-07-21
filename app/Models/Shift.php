<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $casts = [
        'have_truck' => 'boolean',
        'have_chassis' => 'boolean',
        'have_box' => 'boolean'
    ];

    protected $fillable = [
        'driver_id',
        'turn_id',
        'timezone_id',
        'have_truck',
        'truck_number',
        'have_chassis',
        'chassis_type_id',
        'chassis_number',
        'have_box',
        'box_status',
        'box_type_id',
        'box_number',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function boxType(): BelongsTo
    {
        return $this->belongsTo(BoxType::class);
    }

    public function chassisType(): BelongsTo
    {
        return $this->belongsTo(ChassisType::class);
    }

}
