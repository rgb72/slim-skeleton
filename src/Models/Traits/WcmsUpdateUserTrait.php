<?php

namespace App\Models\Traits;

use App\Models\WcmsUser;

trait WcmsUpdateUserTrait {

    public function updateUser() {
        return $this->belongsTo(WcmsUser::class, 'updated_by');
    }

}
