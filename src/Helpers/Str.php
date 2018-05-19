<?php

namespace App\Helpers;

class Str {

    public static function random($length = 25) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);
    }

    public static function decamelize($string, $splitter = '_') {
        $string = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $string));
        return strtolower($string);
    }

    public static function camelize($string, $splitter = '_') {
        return str_replace($splitter, '', ucwords($string, $splitter));
    }

}
