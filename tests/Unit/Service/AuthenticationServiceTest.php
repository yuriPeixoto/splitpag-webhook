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

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        // Mock environment variables
        $_ENV['SPLITPAG_API_URL'] = 'https://test-api.example.com';
        $_ENV['SPLITPAG_EMAIL'] = 'test@example.com';
        $_ENV['SPLITPAG_PASSWORD'] = 'test_password';
    }

    public function testSucessfulLogin()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['token' => 'test_token'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->setConstructorArgs([$this->logger])
            ->onlyMethods(['createHttpClient'])
            ->getMock();

        $authService->method('createHttpClient')->willReturn($client);

        $token = $authService->getToken();

        $this->assertEquals('test_token', $token);
    }

    public function testFailedLogin()
    {
        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new \GuzzleHttp\Psr7\Request('POST', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->setConstructorArgs([$this->logger])
            ->onlyMethods(['createHttpClient'])
            ->getMock();

        $authService->method('createHttpClient')->willReturn($client);

        $this->expectException(\App\Exception\AuthenticationException::class);

        $authService->getToken();
    }
}
