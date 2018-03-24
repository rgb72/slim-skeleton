<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcmsModule extends Model {

    public $timestamps = false;

    public function child() {
        return $this->hasMany(__CLASS__, 'parent_id')->orderBy('ordering');
    }

    public function parent() {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

}
