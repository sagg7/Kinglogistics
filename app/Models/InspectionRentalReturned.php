<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InspectionRentalReturned extends Pivot
{
    use S3Functions;

    protected $table = 'inspection_rental_return';

    /**
     * @param $value
     * @return string
     */
    public function getOptionValueAttribute($value)
    {
        if ($value) {
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
