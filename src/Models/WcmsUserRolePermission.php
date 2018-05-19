<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcmsUserRolePermission extends Model {

    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'action_view',
        'action_create',
        'action_update',
        'action_delete',
        'action_export'
    ];

    public function module() {
        return $this->belongsTo(WcmsModule::class, 'module_id');
    }

    public function role() {
        return $this->belongsTo(WcmsUserRole::class, 'role_id');
    }

}
