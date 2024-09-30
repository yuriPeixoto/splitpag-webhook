<?php

declare(strict_types=1);

namespace App\Handler;

use App\Api\SplitPagClient;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class ChargeHandler
{
    public function __construct(
        private SplitPagClient $splitPagClient,
        private LoggerInterface $logger
    ) {
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $result = $this->splitPagClient->createCharge($data);
            $this->logger->info('Charge created', $result);
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->logger->error('Failed to create charge', ['error' => $e->getMessage()]);
            $response->getBody()->write(json_encode(['error' => 'Failed to create charge']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
