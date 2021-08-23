<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use phpDocumentor\Reflection\Types\Boolean;

class Driver extends Authenticatable implements CanResetPassword
{
    use HasFactory, HasApiTokens, SoftDeletes, Notifiable;

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'inactive' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }
    
    /**
     * @return belongsTo
     */
    public function turn(): BelongsTo
    {
        return $this->belongsTo(Turn::class);
    }

    /**
     * @return HasMany
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * @return HasMany
     */
    public function loads(): HasMany
    {
        return $this->hasMany(Load::class);
    }

    /**
     * @return HasOne
     */
    public function trailer(): HasOne
    {
        return $this->hasOne(Trailer::class);
    }

    /**
     * @return HasOne
     */
    public function availableDriver(): HasOne
    {
        return $this->hasOne(AvailableDriver::class);
    }

    /**
     * @return HasOne
     */
    public function truck(): HasOne
    {
        return $this->hasOne(Truck::class);
    }

    /**
     * @return BelongsTo
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * @return BelongsToMany
     */
    public function shippers(): BelongsToMany
    {
        return $this->belongsToMany(Shipper::class);
    }

    /**
     * @return HasMany
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Gets all the rejected loads of driver
     *
     * @return HasMany
     */

    public function rejections(): HasMany
    {
        return $this->hasMany(RejectedLoad::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }

    /**
     *
     * Helpers
     *
     */

    public function hasActiveShift(): bool
    {
        return !empty($this->availableDriver) && !empty($this->shift);
    }

    public function canActiveShift(): bool
    {
        $turn = $this->turn;
        $now = Carbon::now();

        if ($turn->start->isAfter($turn->end)) {
            // The shift is "broken" in two different days by midnight, should do an extra validation
            $canActivate =
                // The current moment is after the start of the turn and has not passed the midnight
                $now->isAfter($turn->start) && $now->isAfter($turn->end)
                ||
                // The current moment is before the end of the turn and has been passed the midnight
                $now->isBefore($turn->end) && $now->isBefore($turn->start);
        } else {
            // Normal turn, just check between times
            $canActivate = $now->isBetween($turn->start, $turn->end);
        }

        return $canActivate;
    }
}
