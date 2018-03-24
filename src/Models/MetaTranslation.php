<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaTranslation extends Model {

    public $timestamps = false;

    protected $fillable = ['locale', 'title', 'description', 'og_title', 'og_description', 'og_image'];

}
