<?php

namespace App\Helpers\Meta;

use App\Models\Meta;

trait HasMeta {

    public function meta() {
        return $this->hasOne(Meta::class, 'ref_id')->where('page', $this->metaPageName);
    }

}
