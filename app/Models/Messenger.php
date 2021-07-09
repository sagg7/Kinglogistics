<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Messenger extends Authenticatable
{
    use HasFactory;

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function conversation()
    {
    }

}
