<?php

declare(strict_types=1);

use App\Api\SplitPagClient;
use GuzzleHttp\Client as HttpClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => function (ContainerInterface $c) {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/app.log', Logger::DEBUG));
        return $logger;
    },

    HttpClient::class => function (ContainerInterface $c) {
        return new HttpClient();
    },

    SplitPagClient::class => function (ContainerInterface $c) {
        return new SplitPagClient(
            $c->get(HttpClient::class),
            $_ENV['SPLITPAG_API_URL']
        );
    },
];
