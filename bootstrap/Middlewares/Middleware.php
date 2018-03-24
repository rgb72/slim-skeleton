<?php

namespace Bootstrap\Middlewares;

class Middleware {

    protected static $config = [];

    public static function inject($provider, \Slim\App $app) {
        static::$config = require __DIR__.'/../../config/middleware.php';

        static::injectDependencies('base', $app);
        static::injectDependencies($provider, $app);
        static::injectDependencies('debug', $app);
    }

    protected static function injectDependencies($provider, \Slim\App $app) {
        if(!isset(static::$config[$provider])) return;

        foreach (static::$config[$provider] as $class) {
            $app->add(new $class($app));
        }
    }

}
