<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configure extends Model {

    protected $fillable = ['variable', 'value'];

    public $timestamps = false;

}
