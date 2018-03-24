<?php

namespace Bootstrap\Containers;

class Container {

    protected static $config = [];

    public static function inject($provider, \Slim\Container $container) {
        static::$config = require __DIR__.'/../../config/container.php';

        static::injectDependencies('base', $container);
        static::injectDependencies($provider, $container);
        static::injectDependencies('debug', $container);
    }

    protected static function injectDependencies($provider, \Slim\Container $container) {
        if(!isset(static::$config[$provider])) return;

        foreach (static::$config[$provider] as $class) {
            $class = new $class;
            $class($container);
        }
    }

}
