<?php

namespace Bootstrap;

class App {

    public function __construct($provider) {
        $settings = require __DIR__.'/../config/setting.php';

        $container = [
            'settings' => $settings
        ];

        $app = new \Slim\App($container);

        Containers\Container::inject($provider, $app->getContainer());
        Middlewares\Middleware::inject($provider, $app);

        $route = sprintf('%s/../routes/%s.php', __DIR__, $provider);
        if(realpath($route)) require $route;

        $this->app = $app;
    }

    public function __call($name, $arguments) {
        return $this->app->{$name}(...$arguments);
    }

}
