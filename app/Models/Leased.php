<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leased extends Model
{
    use HasFactory;
    // Referencia a tabla en la bd
    protected $table = 'leased';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ["id", "name", "email", "phone", "address"];

}
