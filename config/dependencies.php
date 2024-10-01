<?php

declare(strict_types=1);

use App\Api\SplitPagApi;
use App\Service\AuditService;
use App\Service\AuthenticationService;
use App\Service\ClientService;
use App\Service\ChargeService;
use App\Service\PaymentService;
use App\Service\WebhookProcessor;
use App\Handler\WebhookHandler;
use GuzzleHttp\Client as HttpClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [
    Logger::class => function (ContainerInterface $c) {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/app.log', Logger::DEBUG));
        return $logger;
    },

    AuthenticationService::class => function (ContainerInterface $c) {
        return new AuthenticationService($c->get(Logger::class));
    },

    SplitpagApi::class => function (ContainerInterface $c) {
        return new SplitpagApi(
            $c->get(Logger::class),
            $c->get(AuthenticationService::class)
        );
    },

    ClientService::class => function (ContainerInterface $c) {
        return new ClientService(
            $c->get(SplitpagApi::class),
            $c->get(Logger::class)
        );
    },

    ChargeService::class => function (ContainerInterface $c) {
        return new ChargeService(
            $c->get(SplitpagApi::class),
            $c->get(Logger::class)
        );
    },

    PaymentService::class => function (ContainerInterface $c) {
        return new PaymentService(
            $c->get(SplitpagApi::class),
            $c->get(Logger::class)
        );
    },

    AuditService::class => function (ContainerInterface $c) {
        return new AuditService(
            $c->get(SplitpagApi::class),
            $c->get(Logger::class)
        );
    },

    ResponseFactoryInterface::class => function () {
        return new ResponseFactory();
    },

    WebhookProcessor::class => function (ContainerInterface $c) {
        return new WebhookProcessor(
            $c->get(Logger::class),
            $c->get(ClientService::class),
            $c->get(ChargeService::class),
            $c->get(PaymentService::class)
        );
    },

    WebhookHandler::class => function (ContainerInterface $c) {
        return new WebhookHandler(
            $c->get(AuthenticationService::class),
            $c->get(WebhookProcessor::class),
            $c->get(Logger::class),
            $c->get(ResponseFactoryInterface::class)
        );
    },
];
