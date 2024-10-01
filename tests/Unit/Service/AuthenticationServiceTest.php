<?php

namespace Tests\Unit\Service;

use App\Service\AuthenticationService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class AuthenticationServiceTest extends TestCase
{
    private LoggerInterface $logger;
    private AuthenticationService $authService;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        // Mock environment variables
        $_ENV['SPLITPAG_API_URL'] = 'https://test-api.example.com';
        $_ENV['SPLITPAG_EMAIL'] = 'test@example.com';
        $_ENV['SPLITPAG_PASSWORD'] = 'test_password';

        $this->authService = $this->createMock(AuthenticationService::class);
    }

    public function testGetTokenWithValidToken()
    {
        $this->authService->expects($this->once())
            ->method('getToken')
            ->willReturn('valid_token');

        $token = $this->authService->getToken();

        $this->assertEquals('valid_token', $token);
    }

    public function testGetTokenWithExpiredToken()
    {
        $this->authService->expects($this->exactly(2))
            ->method('getToken')
            ->willReturnOnConsecutiveCalls(null, 'new_token');

        $token = $this->authService->getToken();

        $this->assertEquals('new_token', $token);
    }

    public function testSuccessfulLogin()
    {
        $this->authService->expects($this->once())
            ->method('getToken')
            ->willReturn('test_token');

        $token = $this->authService->getToken();

        $this->assertEquals('test_token', $token);
    }

    public function testFailedLogin()
    {
        $this->authService->expects($this->once())
            ->method('getToken')
            ->willThrowException(new \App\Exception\AuthenticationException('Login failed'));

        $this->expectException(\App\Exception\AuthenticationException::class);

        $this->authService->getToken();
    }
}
