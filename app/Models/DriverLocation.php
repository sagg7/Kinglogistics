<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DriverLocation extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    public $fillable = [
        'latitude',
        'longitude',
        'status',
        'driver_id',
        'load_id',
    ];

    /**
     * @return BelongsTo
     */

    public function parentLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class, 'load_id');
    }

    /**
     * @return BelongsTo
     */

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

}
