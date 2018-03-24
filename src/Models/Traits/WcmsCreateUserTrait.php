<?php

namespace App\Models\Traits;

use App\Models\WcmsUser;

trait WcmsCreateUserTrait {

    public function createUser() {
        return $this->belongsTo(WcmsUser::class, 'created_by');
    }

}
