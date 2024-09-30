<?php

declare(strict_types=1);

namespace App\Api;

use GuzzleHttp\Client as HttpClient;

class SplitPagClient
{
    public function __construct(
        private HttpClient $httpClient,
        private string $apiUrl,
        private string $apiKey
    ) {
    }

    public function createCharge(array $data): array
    {
        $response = $this->httpClient->post($this->apiUrl . '/v1/charges', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => $data,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
