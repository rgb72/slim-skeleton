<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcmsUser extends Model {

    protected $guarded = ['id'];

    protected $hidden = ['password'];

    public function scopeActive($query) {
        $query->where('avaliable', true);
    }

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = sha1(trim($password));
    }

    public function keys() {
        return $this->hasMany(WcmsUserKey::class, 'user_id');
    }

    public function role() {
        return $this->belongsTo(WcmsUserRole::class, 'role_id');
    }

}
