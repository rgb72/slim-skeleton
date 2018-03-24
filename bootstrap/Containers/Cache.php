<?php

namespace Bootstrap\Containers;

class Cache {

    public function __invoke($container) {
        $container['cache'] = function($container) {
            $setting = require_once __DIR__.'/../../config/cache.php';

            $c = new \Illuminate\Container\Container;
            $c['files'] = new \Illuminate\Filesystem\Filesystem;
            $c['config'] = $setting['config'];
            $c['path.storage'] = $setting['config']['path.storage'];

            $cache_manager = new \Illuminate\Cache\CacheManager($c);
            $cache = $cache_manager->driver();

            return $cache;
        };
    }

}
