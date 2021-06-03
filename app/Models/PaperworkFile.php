<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperworkFile extends Model
{
    use HasFactory;

    protected $appends = ['file_name'];

    public function getFileNameAttribute()
    {
        $exploded = explode('/', $this->url);
        return $exploded[count($exploded) - 1];
    }

    /**
     * @return BelongsTo
     */
    public function parentPaperwork(): BelongsTo
    {
        return $this->belongsTo(Paperwork::class, 'paperwork_id');
    }
}
