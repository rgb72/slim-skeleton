<?php

namespace App\Site\Controllers;

class HomeController extends BaseController {

    public function index($request, $response, $args) {
        return $this->view->render($response, 'home/main.twig');
    }

    public function show($request, $response, $args) {
        return $this->view->render($response, 'home/main.twig', ['name' => $args['name']]);
    }

}
