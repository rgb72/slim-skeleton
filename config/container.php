<?php

return [
    'base' => [
        Bootstrap\Containers\Logger::class,
        Bootstrap\Containers\Cache::class,
        Bootstrap\Containers\Database::class
    ],

    'site' => [
        App\Site\Containers\Template::class
    ],

    'console' => [
        App\Console\Containers\MockupEnvironment::class
    ]
];
