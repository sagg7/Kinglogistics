<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;
    // Referencia a tabla en la bd
    protected $table = 'rentals';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ["id", "rental_date", "cost", "deposit_amount", "is_paid", "periodicity", "valid_until", "trailer_id", "leased_id", "driver_id"];

    public function inspectionItems()
    {
        return $this->belongsToMany('App\Models\InspectionItem', 'inspection_rental', 'rental_id', 'inspection_item_id')
            ->withPivot(['option_value']);
    }
}
