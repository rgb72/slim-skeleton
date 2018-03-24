<?php

namespace App\Site\Controllers;

class BaseController {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
        $this->view = $container->get('view');

        $this->setTwigGlobalVariable();
    }

    protected function setTwigGlobalVariable() {
        $twig = $this->view->getEnvironment();

        $twig->addGlobal('current_lang', $this->container->lang);
        $twig->addGlobal('current_url', (string)$this->container->request->getUri()->withQuery(''));
    }

}
