<?php

use App\Api\Controllers;

$app->group('/api', function() use ($app) {

    $app->get('', Controllers\HomeController::class.':index');

});
