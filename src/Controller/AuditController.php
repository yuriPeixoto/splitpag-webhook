<?php

namespace App\Controller;

use App\Service\AuditService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuditController
{
    private $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function getAudits(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? '1';
        $search = $queryParams['search'] ?? '';

        $audits = $this->auditService->getAudits($page, $search);

        $response->getBody()->write(json_encode($audits));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
