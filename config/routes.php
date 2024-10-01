<?php

declare(strict_types=1);

use App\Controller\AuditController;
use App\Controller\ClientController;
use App\Controller\ChargeController;
use App\Controller\PaymentController;
use App\Handler\WebhookHandler;
use App\Middleware\AuthenticationMiddleware;
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Webhook route (unprotected)
    $app->post('/webhook', function (Request $request, Response $response, WebhookHandler $handler) {
        return $handler->handle($request);
    });
    
    // Protected routes group
    $app->group('', function ($app) {
        // Client routes
        $app->get('/client', [ClientController::class, 'getClients']);
        $app->post('/client', [ClientController::class, 'createClient']);

        // Charge routes
        $app->get('/charge', [ChargeController::class, 'getCharges']);
        $app->get('/charge/create', [ChargeController::class, 'getChargeCreateData']);
        $app->post('/charge/create', [ChargeController::class, 'createCharge']);
        $app->get('/charge/status/{hash_charge_id}', [ChargeController::class, 'getChargeStatus']);

        // Payment routes
        $app->get('/payment', [PaymentController::class, 'getPayments']);
        $app->get('/payment/checkStatusPayment', [PaymentController::class, 'checkStatusPayment']);

        // Audit route
        $app->get('/audit', [AuditController::class, 'getAudits']);
    })->add(AuthenticationMiddleware::class);
};
