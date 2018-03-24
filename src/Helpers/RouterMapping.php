<?php

namespace App\Helpers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Exception;

class RouterMapping {

    public static $version = null;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, Array $args) {
        $version = isset($args['version']) ? $args['version'] : static::$version;

        $controller = sprintf('\App\%s\Resources\Resource', $version);
        if(!class_exists($controller)) return $response->withStatus(404);

        $controller = new $controller($this->container);

        return $controller($request, $response, $args);
    }

}
