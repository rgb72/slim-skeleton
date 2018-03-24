<?php

namespace App\Site\Containers;

use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;
use Twig_Extensions_Extension_Text;
use Twig_Extension_StringLoader;
use App\Helpers\Twig\Filters\TwigFilter;
use App\Helpers\Twig\Functions\TwigFunction;

class Template {

    public function __invoke($container) {
        $container['view'] = function($container) {
            $setting = require_once __DIR__.'/../../../config/view.php';

            $view = new Twig(
                $setting['directory'],
                $setting['options']
            );

            $view->addExtension(new TwigExtension(
                $container['router'],
                $container['request']->getUri()
            ));

            $view->addExtension(new Twig_Extension_Debug);
            $view->addExtension(new Twig_Extensions_Extension_Text);
            $view->addExtension(new Twig_Extension_StringLoader);

            $twig = new \App\Helpers\Twig\Twig($container);

            $view->getEnvironment()->addFunction($twig->staticFunction());
            $view->getEnvironment()->addFilter($twig->datetimeFilter());
            $view->getEnvironment()->addFilter($twig->withUrl());

            return $view;
        };
    }

}
