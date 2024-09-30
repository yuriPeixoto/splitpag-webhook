<?php

declare(strict_types=1);

use App\Controller\ClientController;
use Slim\App;

return function (App $app) {
    $app->get('/client', [ClientController::class, 'getClients']);
    $app->post('/client', [ClientController::class, 'createClient']);
};
