<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoadStatus extends Model
{
    use HasFactory;

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

    /**
     * Establish a relationship with the Load model, has to be named like "parentLoad" due overlapping "load()" Laravel method.
     *
     * @return BelongsTo
     */

    public function parentLoad(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

}
