<?php

namespace App\Api\Controllers;

class HomeController {

    public function index($request, $response, $args) {
        return $response->withJson(['message' => 'Hello world']);
    }

}
