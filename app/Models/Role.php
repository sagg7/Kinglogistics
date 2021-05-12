<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Permissions\RoleTrait;

class Role extends Model
{
    use RoleTrait;

    public function permissions() {
        return $this->belongsToMany(Permission::class,'permission_role');
    }

    public function users() {

        return $this->belongsToMany(User::class,'permission_user');

    }
}
