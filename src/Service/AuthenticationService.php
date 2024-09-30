<?php

namespace App\Service;

use GuzzleHttp\Client;
use App\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class AuthenticationService
{
    private $client;
    private $logger;
    private $apiUrl;
    private $email;
    private $password;
    private $token;
    private $tokenExpiration;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger   = $logger;
        $this->apiUrl   = $_ENV['SPLITPAG_API_URL'];
        $this->email    = $_ENV['SPLITPAG_EMAIL'];
        $this->password = $_ENV['SPLITPAG_PASSWORD'];
    }

    protected function createHttpClient(): Client
    {
        return new Client(['verify' => false]);
    }

    public function getToken(): string
    {
        if ($this->token && $this->tokenExpiration > time()) {
            return $this->token;
        }

        $this->login();
        return $this->token;
    }

    private function login(): void
    {
        try {
            $client = $this->createHttpClient();
            $response = $client->post($this->apiUrl . '/login', [
                'json' => [
                    'email'    => $this->email,
                    'password' => $this->password,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['token'])) {
                $this->token = $data['token'];
                $this->tokenExpiration = time() + 3600;
            } else {
                throw new AuthenticationException('Invalid response from SplitPag API');
            }
        } catch (\Exception $e) {
            $this->logger->error('Authentication failed: ' . $e->getMessage());
            throw new AuthenticationException('Failed to authenticate with Splitpag API');
        }
    }
}
