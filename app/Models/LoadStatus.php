<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoadStatus extends Model
{
    use HasFactory, S3Functions;

    public $fillable = [
        'load_id',
        'unallocated_timestamp',
        'requested_timestamp',
        'accepted_timestamp',
        'loading_timestamp',
        'to_location_timestamp',
        'arrived_timestamp',
        'unloading_timestamp',
        'finished_timestamp',
        'to_location_voucher',
        'finished_voucher',
    ];

    protected $appends = [
        'to_location_voucher_image_url',
        'finished_voucher_image_url'
    ];

    /**
     * Establish a relationship with the Load model, has to be named like "parentLoad" due overlapping "load()" Laravel method.
     *
     * @return BelongsTo
     */

    public function parentLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    /*
     * Mutators
     */

    public function getToLocationVoucherImageUrlAttribute(): ?string
    {
        return !empty($this->to_location_voucher) ? $this->getTemporaryFile($this->to_location_voucher) : null;
    }

    public function getFinishedVoucherImageUrlAttribute(): ?string
    {
        return !empty($this->finished_voucher) ? $this->getTemporaryFile($this->finished_voucher) : null;
    }
}