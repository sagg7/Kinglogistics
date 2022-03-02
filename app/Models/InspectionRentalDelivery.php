<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InspectionRentalDelivery extends Pivot
{
    use S3Functions;

    protected $table = 'inspection_rental_delivery';

    /**
     * @param $value
     * @return string
     */
    public function getOptionValueAttribute($value)
    {
        if ($value !== null) {
            switch ($this->inspection_item_id) {
                // Case for base64 signatures
                case 40:
                case 41:
                    return $this->getTemporaryFile($value);
                default:
                    return $value;
            }
        } else {
            return null;
        }
    }
}
