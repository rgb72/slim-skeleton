<?php

#Kint
if(class_exists(Kint::class)) {
    function ddd(...$v) {
        d(...$v);
        exit;
    }

    function sd(...$v) {
        s(...$v);
        exit;
    }

    \Kint::$aliases[] = 'ddd';
    \Kint::$aliases[] = 'sd';
}
