<?php

namespace App\Models;

use App\Enums\LoadStatusEnum;
use App\Enums\RentalStatusEnums;
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

    //protected $appends = ['shippers_ids'];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class)->withTrashed();
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
     * @return HasOne
     */
    public function active_rental(): HasOne
    {
        return $this->hasOne(Rental::class)->where('status', '!=', RentalStatusEnums::FINISHED);
    }

    /**
     * @return HasMany
     */
    public function loads(): HasMany
    {
        return $this->hasMany(Load::class);
    }
    /**
     * @return HasMany
     */
    public function loadStatus(): HasMany
    {
        return $this->hasMany(Load::class)->join('load_statuses', 'loads.id','=','load_statuses.load_id');
    }

    /**
     * @return HasOne
     */
    public function latestLoad(): HasOne
    {
        return $this->hasOne(Load::class)->latest();
    }

    /**
     * @return HasOne
     */
    public function active_load(): HasOne
    {
        return $this->hasOne(Load::class)->where('status', '!=', LoadStatusEnum::FINISHED);
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

    public function getShippersIdsAttribute()
    {
        return $this->shippers()->pluck('id');
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


    public function latestRejection(): HasOne
    {
        return $this->hasOne(RejectedLoad::class)->latest();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(DriverLocation::class)->latest();
    }

    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }

    public function workedHour(): HasMany
    {
        return $this->hasMany(DriverWorkedHour::class);
    }

    public function activeWorkedHour(): HasOne
    {
        return $this->hasOne(DriverWorkedHour::class)->whereNull('shift_end');
    }

    public function safetyMessages(): BelongsToMany
    {
        return $this->belongsToMany(SafetyMessage::class);
    }

    /**
     *
     * Helpers
     *
     */

    public function hasActiveLoads(): bool
    {
        $loadsTotal = $this->loads()->whereNotIn('status', [
            LoadStatusEnum::UNALLOCATED,
            LoadStatusEnum::FINISHED,
        ])->count();

        return $loadsTotal > 0;
    }

    public function isShiftActive(): bool
    {
        return !!Shift::where('driver_id', $this->id)->first();
    }

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
                ($now->isAfter($turn->start) && $now->isAfter($turn->end))
                ||
                // The current moment is before the end of the turn and has been passed the midnight
                ($now->isBefore($turn->end) && $now->isBefore($turn->start));
        } else {
            // Normal turn, just check between times
            $canActivate = $now->isBetween($turn->start, $turn->end);
        }

        return $canActivate;
    }

    public function rejectionCheck(): bool
    {
        $now = Carbon::now();
        return !$this->latestRejection || $now->isAfter($this->latestRejection->created_at->addHours(12));
    }

    public function botAnswer(): HasOne
    {
        return $this->hasOne(BotAnswers::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function worked_hours(): HasMany
    {
        return $this->hasMany(DriverWorkedHour::class);
    }

}
