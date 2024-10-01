<?php

namespace App\Service;

use App\Api\SplitpagApi;
use Psr\Log\LoggerInterface;

class AuditService
{
    private $api;
    private $logger;

    public function __construct(SplitpagApi $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function getAudits(string $page, string $search = ''): array
    {
        $params = ['page' => $page];
        if (!empty($search)) {
            $params['search'] = $search;
        }

        return $this->api->makeRequest('GET', '/audit', $params);
    }
}
