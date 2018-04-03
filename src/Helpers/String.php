<?php

namespace App\Helpers;

class String {

    public static function decamelize($string, $splitter = '_') {
        $string = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $string));
        return strtolower($string);
    }

    public static function camelize($string, $splitter = '_') {
        return str_replace($splitter, '', ucwords($string, $splitter));
    }

}
