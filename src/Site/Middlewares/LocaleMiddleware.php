<?php

namespace App\Site\Middlewares;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

class LocaleMiddleware {

    protected $container;

    public function __construct(App $app) {
        $this->container = $app->getContainer();
    }

    public function __invoke(Request $request, Response $response, $next) {
        if(getenv('IS_MULTIPLE_LANG') !== 'false') {
            $lang = $request->getAttribute('route')->getArgument('lang');
            $this->container['lang'] = !is_null($lang) ? $lang : getenv('LANG_DEFAULT');
        }

        return $next($request, $response);
    }

}
