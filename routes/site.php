<?php

use App\Site\Controllers;

$app->get('/', Controllers\HomeController::class.':index');
$app->get('/{name}', Controllers\HomeController::class.':show');
