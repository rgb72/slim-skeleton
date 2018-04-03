<?php

namespace App\Singletons;

class GlobalVariable implements SingletonInterface {

    protected static $instance = null;
    protected $setter = [];

    public static function getInstance() {
        if(is_null(static::$instance)) static::$instance = new static;
        return static::$instance;
    }

    protected function __construct() {
        $this->locale = getenv('LANG_DEFAULT');

        $avaliable_translations = getenv('LANG_AVALIABLE');
        $avaliable_translations = array_map('trim', explode(',', $avaliable_translations));

        $this->avaliable_translations = $avaliable_translations;
    }

    public function __set($property, $value) {
        $this->setter[$property] = $value;
    }

    public function __get($property) {
        return isset($this->setter[$property]) ? $this->setter[$property] : null;
    }

}
