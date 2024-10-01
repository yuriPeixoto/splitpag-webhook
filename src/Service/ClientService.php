<?php

namespace App\Service;

use App\Api\SplitpagApi;
use Psr\Log\LoggerInterface;

class ClientService
{
    private $api;
    private $logger;

    public function __construct(SplitpagApi $api, LoggerInterface $logger)
    {
        $this->api    = $api;
        $this->logger = $logger;
    }

    public function getClients(string $page, string $search = ''): array
    {
        $params = ['page' => $page];
        if (!empty($search)) {
            $params['search'] = $search;
        }

        return $this->api->makeRequest('GET', '/client', $params);
    }

    public function createClient(array $data): array
    {
        $requiredFields = ['document', 'name', 'email', 'gender', 'birth_date', 'address', 'number_address', 'district', 'city', 'state', 'country', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Field '$field' is required"];
            }
        }

        // Additional validation can be added here

        return $this->api->makeRequest('POST', '/client', $data);
    }
}
