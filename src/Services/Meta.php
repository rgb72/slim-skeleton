<?php

namespace App\Services;

class Meta {

    public function getByPage($page, $ref_id = null) {
        $meta = \App\Models\Meta::where('page', $page)->where(function($query) use ($ref_id) {
            if(!is_null($ref_id)) $query->where('ref_id', $ref_id);
            else $query->whereNull('ref_id');
        })->first();

        return $meta;
    }

}
