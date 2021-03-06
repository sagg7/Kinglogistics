<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'date:m/d/Y',
        'charge_date' => 'date',
        'delivered_at' => 'date:m/d/Y',
        'finished_at' => 'date:m/d/Y',
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
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo
     */
    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class)->withTrashed();
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }  

    public function deliveryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_user_id', 'id')->withTrashed();
    }  
    
    public function returnedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_user_id', 'id')->withTrashed();
    }

    public function inspectionItems()
    {
        return $this->belongsToMany(InspectionItem::class, 'inspection_rental_delivery', 'rental_id', 'inspection_item_id')
            ->withPivot(['option_value'])
            ->using(InspectionRentalDelivery::class);
    }

    public function inspectionItemsReturned()
    {
        return $this->belongsToMany(InspectionItem::class, 'inspection_rental_return', 'rental_id', 'inspection_item_id')
            ->withPivot(['option_value'])
            ->using(InspectionRentalReturned::class);
    }
}
