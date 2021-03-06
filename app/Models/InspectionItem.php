<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionItem extends Model
{
    use SoftDeletes;

    //REFERENCIA A TABLA EN LA BD
    protected $table = 'inspection_items';
    protected $dates = ['deleted_at'];
}
