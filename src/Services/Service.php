<?php

namespace App\Services;

use App\Helpers\Str;
use ErrorException;

class Service {

    public function __get($name) {
        $class_name = sprintf('%s\\%s', __NAMESPACE__, Str::camelize($name));
        if(!class_exists($class_name, true))
            throw new ErrorException(sprintf('Class \'%s\' not found', $class_name));

        $service = new $class_name;
        return $service;
    }

}
