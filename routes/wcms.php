<?php

use App\Helpers\RouterMapping;
use App\Wcms\Middlewares\AuthMiddleware;
use App\Wcms\Controllers;

RouterMapping::$version = 'Wcms';

$app->group('/api', function () use ($app) {

    $app->group('/auth', function () use ($app) {
        $app->post('/login', Controllers\AuthController::class.':login');
        $app->get('/token', Controllers\AuthController::class.':token')->add(new AuthMiddleware);
        $app->delete('/{key}', Controllers\AuthController::class.':logout')->add(new AuthMiddleware);
    });

    $app->group('', function() use ($app) {
        # Member Controller
        $app->group('/me', function () use ($app) {
            $app->get('', Controllers\MeController::class.':show');
            $app->put('', Controllers\MeController::class.':update');
            $app->get('/modules', Controllers\MeController::class.':modules');
            $app->get('/modules/{name}', Controllers\MeController::class.':moduleInfo');
        });

        $app->post('/uploads', Controllers\UploadController::class.':store');

        $app->any('/{resource:.*}', RouterMapping::class);
    })->add(new AuthMiddleware);
});
