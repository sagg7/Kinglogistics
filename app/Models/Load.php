<?php

namespace App\Models;

use App\Enums\LoadStatusEnum;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Load extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'control_number',
        'customer_reference',
        'bol',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'date:m/d/Y',
        'weight' => 'decimal:2',
        'mileage' => 'decimal:2',
        'rate' => 'decimal:2',
        'shipper_rate' => 'decimal:2',
        'auto_assigned' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)
            ->withTrashed();
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

    /**
     * @return BelongsTo
     */
    public function shipper_invoice(): BelongsTo
    {
        return $this->belongsTo(ShipperInvoice::class);
    }

    /**
     * @return BelongsTo
     */
    public function carrier_payment(): BelongsTo
    {
        return $this->belongsTo(CarrierPayment::class);
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
     * Get all the load rejections
     *
     * @return HasMany
     */

    public function photos(): HasMany
    {
        return $this->hasMany(LoadPhoto::class);
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

    public function boxInit(): HasOne
    {
        return $this->hasOne(BoxType::class, 'id', 'box_type_id_init');
    }

    public function boxEnd(): HasOne
    {
        return $this->hasOne(BoxType::class, 'id', 'box_type_id_end');
    }

    public function getNotifiedAtProperty(): ?Carbon
    {
        $loadId = $this->id;
        $driver = $this->driver;

        if (empty($driver))
            return null;

        $notifications = $driver->notifications;

        if (empty($notifications))
            return null;

        $loadNotification = $notifications->first(function ($n) use ($loadId) {
            $data = $n->data;
            if (empty($data) || !isset($data['load']))
                return null;

            $load = $data['load'];

            if (empty($load))
                return null;

            return $load['id'] == $loadId;
        });

        if (empty($loadNotification))
            return null;

        return $loadNotification->created_at ?: null;
    }

}
