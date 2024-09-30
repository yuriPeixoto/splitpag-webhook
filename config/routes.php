<?php

declare(strict_types=1);

use App\Handler\ChargeHandler;
use Slim\App;

return function (App $app) {
    $app->post('/charges', [ChargeHandler::class, 'create']);
};
