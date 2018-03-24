<?php

return [
    'base' => [
        Bootstrap\Middlewares\TrailingSlash::class
    ],

    'site' => [
        App\Site\Middlewares\LocaleMiddleware::class
    ],

    'debug' => [
        Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware::class
    ]
];
