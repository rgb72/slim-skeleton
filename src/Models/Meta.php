<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Translatable\Translatable;

class Meta extends Model {
    use Translatable;

    protected $fillable = ['page', 'ref_id', 'created_by', 'updated_by'];

    protected $translation_key = 'meta_id';
    protected $translation_fields = ['title', 'description', 'og_title', 'og_description', 'og_image'];

}
