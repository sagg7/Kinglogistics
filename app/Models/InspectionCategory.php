<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionCategory extends Model
{

    //REFERENCIA A TABLA EN LA BD
    protected $table = 'inspection_categories';
    protected $primaryKey = 'id';

    public function items()
    {
        return $this->hasMany('App\Models\InspectionItem', 'inspection_category_id');
    }
}
