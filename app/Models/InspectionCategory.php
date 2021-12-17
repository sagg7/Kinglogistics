<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionCategory extends Model
{

    //REFERENCIA A TABLA EN LA BD
    protected $primaryKey = 'id';

    public function items()
    {
        return $this->hasMany(InspectionItem::class);
    }
}
