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

    public function validateToken(string $token): bool
    {
        // If the token matches our current token and it's not expired, it's valid
        if ($token === $this->token && time() < $this->tokenExpiration) {
            return true;
        }

        // If it doesn't match our current token or our token is expired, try to validate it with the API
        try {
            $response = $this->client->get($this->apiUrl . '/validate-token', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // If the API says the token is valid, update our token and expiration
            if (isset($data['valid']) && $data['valid']) {
                $this->token = $token;
                $this->tokenExpiration = time() + 3600; // Assume token is valid for 1 hour
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error('Token validation failed: ' . $e->getMessage());
        }

        return false;
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
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $context = [
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse() : null,
            ];
            $this->logger->error('Authentication failed: ' . $e->getMessage(), $context);
            throw new AuthenticationException('Failed to authenticate with Splitpag API', 0, $e, $context);
        } catch (\Exception $e) {
            $this->logger->error('Authentication failed: ' . $e->getMessage());
            throw new AuthenticationException('Unexpected error during authentication', 0, $e);
        }
    }
}
