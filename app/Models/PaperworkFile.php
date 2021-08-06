<?php

namespace App\Models;

use App\Traits\Storage\S3Functions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperworkFile extends Model
{
    use HasFactory, S3Functions;

    protected $appends = ['file_name'];

    public function getFileNameAttribute()
    {
        $exploded = explode('/', $this->url);
        return explode('?', $exploded[count($exploded) - 1])[0];
    }

    /*public function getUrlAttribute($value)
    {
        if ($value)
            return $this->getTemporaryFile($value);
    }*/

    /**
     * @return BelongsTo
     */
    public function parentPaperwork(): BelongsTo
    {
        return $this->belongsTo(Paperwork::class, 'paperwork_id');
    }
}
