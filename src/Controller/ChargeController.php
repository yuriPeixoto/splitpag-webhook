<?php

namespace App\Controller;

use App\Service\ChargeService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ChargeController
{
    private $chargeService;

    public function __construct(ChargeService $chargeService)
    {
        $this->chargeService = $chargeService;
    }

    public function getCharges(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? '1';
        $search = $queryParams['search'] ?? '';
        $from = $queryParams['from'] ?? '';
        $to = $queryParams['to'] ?? '';
        $status = $queryParams['status'] ?? '';

        $charges = $this->chargeService->getCharges($page, $search, $from, $to, $status);

        $response->getBody()->write(json_encode($charges));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getChargeCreateData(Request $request, Response $response): Response
    {
        $data = $this->chargeService->getChargeCreateData();

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createCharge(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $result = $this->chargeService->createCharge($data);

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['success'] ? 201 : 400);
    }

    public function getChargeStatus(Request $request, Response $response, array $args): Response
    {
        $hashChargeId = $args['hash_charge_id'];

        $status = $this->chargeService->getChargeStatus($hashChargeId);

        $response->getBody()->write(json_encode($status));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
