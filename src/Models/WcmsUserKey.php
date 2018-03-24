<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcmsUserKey extends Model {

    protected $dates = ['expired_at'];

    public function user() {
        return $this->belongsTo(WcmsUser::class, 'user_id');
    }

}
