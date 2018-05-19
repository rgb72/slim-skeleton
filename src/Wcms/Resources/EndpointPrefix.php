<?php

namespace App\Wcms\Resources;

use App\Models;
use App\Wcms\Controllers;

class EndpointPrefix {

    protected $class_names = [
        'wcms-modules'    => Models\WcmsModule::class,
        'wcms-users'      => Models\WcmsUser::class,
        'wcms-user-roles' => Models\WcmsUserRole::class
    ];

    public function getClassName($prefix) {
        return isset($this->class_names[$prefix]) ? $this->class_names[$prefix] : null;
    }

}
