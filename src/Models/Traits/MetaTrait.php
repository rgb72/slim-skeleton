<?php

namespace App\Models\Traits;

use App\Models\Meta;

trait MetaTrait {

    public function getMetaPage() {
        if(!property_exists($this, 'meta_page')) throw new Exception('meta_page is not declare.');
        return $this->meta_page;
    }

    public function meta() {
        $meta_page = $this->getMetaPage();
        return $this->hasOne(Meta::class, 'ref_id')->where('page', $meta_page);
    }

}
