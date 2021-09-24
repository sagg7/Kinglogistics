<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes, S3Functions;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    /**
     * @param $value
     * @return string
     */
    public function getSafetySignatureAttribute($value)
    {
        if ($value)
            return $this->getTemporaryFile($value);
    }

    /**
     * @param $value
     * @return string|void
     */
    public function getDriverSignatureAttribute($value)
    {
        if ($value)
            return $this->getTemporaryFile($value);
    }

    /**
     * @return BelongsTo
     */
    public function incident_type(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

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
    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
