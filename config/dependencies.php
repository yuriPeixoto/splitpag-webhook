<?php

declare(strict_types=1);

use App\Api\SplitPagApi;
use App\Service\AuthenticationService;
use App\Service\ClientService;
use App\Service\WebhookProcessor;
use GuzzleHttp\Client as HttpClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

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

    WebhookProcessor::class => function (ContainerInterface $c) {
        return new WebhookProcessor(
            $c->get(SplitpagApi::class),
            $c->get(Logger::class)
        );
    },
];
