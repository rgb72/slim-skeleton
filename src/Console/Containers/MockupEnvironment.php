<?php

namespace App\Console\Containers;

class MockupEnvironment {

    public function __invoke($container) {
        $argv = $GLOBALS['argv'];
        array_shift($argv); # Remove script name
        $request_uri = (!empty($argv) ? implode('/', $argv) : '');
        $request_uri = (!empty($request_uri) && $request_uri[0] !== '/' ? '/'. $request_uri : $request_uri);
        $environment = \Slim\Http\Environment::mock(['REQUEST_URI' => $request_uri]);

        $container['environment'] = $environment;
    }

}
