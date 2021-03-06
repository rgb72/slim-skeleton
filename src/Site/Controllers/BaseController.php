<?php

namespace App\Site\Controllers;

use App\Models\Configure;
use App\Models\Meta;

class BaseController {

    protected $container;
    protected $view;
    protected $config;

    public function __construct($container) {
        $this->container = $container;
        $this->view      = $container->get('view');

        $this->getConfig();

        $this->setTwigGlobalVariable();
        $this->setMeta('default');
    }

    protected function setTwigGlobalVariable() {
        $twig = $this->view->getEnvironment();

        if($this->container->has('lang'))
            $twig->addGlobal('current_lang', $this->container->lang);
        $twig->addGlobal('current_url', (string)$this->container->request->getUri()->withQuery(''));
    }

    protected function getConfig() {
        $this->config = Configure::all()->keyBy('variable')->map(function($item) {
            return $item->value;
        });
    }

    protected function setMeta($page, $ref_id = null, Array $replace = []) {
        $meta = Meta::where('page', $page)->where('ref_id', $ref_id)->first();

        if(is_null($meta)) $meta = (object)[];

        foreach ($replace as $key => $value) {
            $meta->{$key} = $value;
        }

        $this->meta = $meta;
        $this->meta->canonical = (string)$this->container->request->getUri();

        $twig = $this->view->getEnvironment();
        $twig->addGlobal('meta', $this->meta);
    }

}
