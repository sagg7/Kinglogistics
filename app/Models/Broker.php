<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Broker extends Model
{
    use HasFactory, S3Functions, SoftDeletes;

    protected $appends = ['insurance_file_name'];

    public function getInsuranceFileNameAttribute()
    {
        $exploded = explode('/', $this->insurance_url);
        return explode('?', $exploded[count($exploded) - 1])[0];
    }

    /**
     * @param $value
     * @return string
     */
    public function getSignatureAttribute($value)
    {
        if ($value)
            return $this->getTemporaryFile($value);
    }

    /**
     * @param $value
     * @return string|void
     */
    public function getInsuranceUrlAttribute($value)
    {
        if ($value)
            return $this->getTemporaryFile($value);
    }
}
