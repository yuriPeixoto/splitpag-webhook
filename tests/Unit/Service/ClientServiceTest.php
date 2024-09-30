<?php

namespace Tests\Unit\Service;

use App\Service\ClientService;
use App\Api\SplitpagApi;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ClientServiceTest extends TestCase
{
    private $apiMock;
    private $loggerMock;
    private $clientService;

    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(SplitpagApi::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->clientService = new ClientService($this->apiMock, $this->loggerMock);
    }

    public function testGetClients(): void
    {
        $expectedResult = ['clients' => []];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/client', ['page' => '1'])
            ->willReturn($expectedResult);

        $result = $this->clientService->getClients('1');
        $this->assertSame($expectedResult, $result);
    }

    public function testCreateClientSuccess(): void
    {
        $clientData = [
            'document' => '123.456.789-00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'gender' => 'm',
            'birth_date' => '1990-01-01',
            'address' => 'Main St',
            'number_address' => '123',
            'district' => 'Downtown',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'phone' => '(123)456-7890'
        ];

        $expectedResult = ['success' => true, 'message' => 'Client created successfully'];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('POST', '/client', $clientData)
            ->willReturn($expectedResult);

        $result = $this->clientService->createClient($clientData);
        $this->assertSame($expectedResult, $result);
    }

    public function testCreateClientMissingField(): void
    {
        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $result = $this->clientService->createClient($clientData);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('is required', $result['message']);
    }
}
