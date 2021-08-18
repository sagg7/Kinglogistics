<?php

namespace App\Models;

use App\Enums\LoadStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Load extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo
     */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * @return BelongsTo
     */
    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    /**
     * @return BelongsTo
     */
    public function load_type(): BelongsTo
    {
        return $this->belongsTo(LoadType::class);
    }

    /**
     * @return BelongsTo
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function loadStatus(): HasOne
    {
        return $this->hasOne(LoadStatus::class);
    }

    /**
     * Get all the load rejections
     *
     * @return HasMany
     */

    public function rejections(): HasMany
    {
        return $this->hasMany(RejectedLoad::class);
    }

    /**
     * Get all the driver locations related to load
     *
     * @return HasMany
     */

    public function locations(): HasMany
    {
        return $this->hasMany(DriverLocation::class);
    }

    /**
     * Get all the driver locations related to load
     *
     * @return HasOne
     */

    public function latestLocation(): HasOne
    {
        return $this->hasOne(DriverLocation::class)->latest();
    }
}
