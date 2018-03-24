<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcmsUserRole extends Model {

    protected $fillable = ['name'];

    public function permissions() {
        return $this->hasMany(WcmsUserRolePermission::class, 'role_id');
    }

    public function user() {
        return $this->hasMany(WcmsUser::class, 'role_id');
    }

}
