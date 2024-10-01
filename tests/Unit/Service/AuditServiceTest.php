<?php

namespace Tests\Unit\Service;

use App\Service\AuditService;
use App\Api\SplitpagApi;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AuditServiceTest extends TestCase
{
    private $apiMock;
    private $loggerMock;
    private $auditService;

    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(SplitpagApi::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->auditService = new AuditService($this->apiMock, $this->loggerMock);
    }

    public function testGetAudits(): void
    {
        $expectedResult = [
            'audits' => [
                [
                    'hash_id' => '1-abc',
                    'user' => 'admin',
                    'method' => 'GET',
                    'action' => 'client',
                    'ip' => '111.111.111',
                    'status' => 'paid',
                    'agent' => 'Firefox',
                    'observation' => 'Sucesso',
                    'created_at' => '01/01/2023 00:00:00'
                ]
            ]
        ];

        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/audit', ['page' => '1'])
            ->willReturn($expectedResult);

        $result = $this->auditService->getAudits('1');
        $this->assertSame($expectedResult, $result);
    }

    public function testGetAuditsWithSearch(): void
    {
        $expectedResult = [
            'audits' => [
                [
                    'hash_id' => '2-def',
                    'user' => 'admin',
                    'method' => 'POST',
                    'action' => 'charge',
                    'ip' => '222.222.222',
                    'status' => 'pending',
                    'agent' => 'Chrome',
                    'observation' => 'Criação de cobrança',
                    'created_at' => '02/01/2023 00:00:00'
                ]
            ]
        ];

        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/audit', ['page' => '1', 'search' => 'cobrança'])
            ->willReturn($expectedResult);

        $result = $this->auditService->getAudits('1', 'cobrança');
        $this->assertSame($expectedResult, $result);
    }
}
