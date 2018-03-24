<?php

return [
    'path.storage'      => __DIR__.'/../storage',
    'cache.default'     => 'file',
    'cache.stores.file' => [
        'driver' => 'file',
        'path'   => __DIR__.'/../storage/cache/variables'
    ]
];
