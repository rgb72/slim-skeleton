<?php

use App\Console\Controllers;

$app->get('/', Controllers\HomeController::class.':index');
