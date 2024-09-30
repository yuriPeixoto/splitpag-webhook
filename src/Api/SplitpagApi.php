<?php

declare(strict_types=1);

namespace App\Api;

use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use App\Service\AuthenticationService;

class SplitpagApi
{
    private $client;
    private $logger;
    private $authService;
    private $apiUrl;

    public function __construct(LoggerInterface $logger, AuthenticationService $authService)
    {
        $this->client = new Client();
        $this->logger = $logger;
        $this->authService = $authService;
        $this->apiUrl = $_ENV['SPLITPAG_API_URL'];
    }

    public function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->authService->getToken();

        try {
            $response = $this->client->request($method, $this->apiUrl . $endpoint, [
                'json' => $data,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
            $this->logger->error('Failed to make request to SplitPag API: ' . $e->getMessage());
            throw $e;
        }
    }
}
