<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionItem extends Model
{
    use SoftDeletes;

    //REFERENCIA A TABLA EN LA BD
    protected $table = 'inspection_items';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    public function order($orderId)
    {
        return $this->hasOne('App\Models\inspectionChassis', 'inspection_item_id', 'id')
            ->where('inspection_order.order_id', $orderId)->get();
    }
}
