<?php

namespace App\Console\Controllers;

class HomeController extends BaseController {

    public function index($request, $response, $args) {
        $this->write('Hello CLI', 'red');
    }

}
