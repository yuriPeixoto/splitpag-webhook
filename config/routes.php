<?php

declare(strict_types=1);

use App\Controller\ClientController;
use App\Controller\ChargeController;

use Slim\App;

return function (App $app) {
    // Authentication route
    $app->post('/login', [AuthController::class, 'login']);

    $app->get('/client', [ClientController::class, 'getClients']);
    $app->post('/client', [ClientController::class, 'createClient']);

    $app->get('/charge', [ChargeController::class, 'getCharges']);
    $app->get('/charge/create', [ChargeController::class, 'getChargeCreateData']);
    $app->post('/charge/create', [ChargeController::class, 'createCharge']);
    $app->get('/charge/status/{hash_charge_id}', [ChargeController::class, 'getChargeStatus']);
};
