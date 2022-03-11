<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'date:m/d/Y',
    ];

    protected $appends = ['loan_file_name'];


    public function getLoanFileNameAttribute()
    {
        $exploded = explode('/', $this->file_loan_url);
        return explode('?', $exploded[count($exploded) - 1])[0];
    }
    /**
     * @return BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }
}
