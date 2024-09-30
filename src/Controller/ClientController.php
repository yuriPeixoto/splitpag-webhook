<?php

namespace App\Controller;

use App\Service\ClientService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClientController
{
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function getClients(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? '1';
        $search = $queryParams['search'] ?? '';

        $clients = $this->clientService->getClients($page, $search);

        $response->getBody()->write(json_encode($clients));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createClient(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $result = $this->clientService->createClient($data);

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['success'] ? 201 : 400);
    }
}
