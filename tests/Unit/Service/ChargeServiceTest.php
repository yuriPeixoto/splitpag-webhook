<?php

namespace Tests\Unit\Service;

use App\Service\ChargeService;
use App\Api\SplitpagApi;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ChargeServiceTest extends TestCase
{
    private $apiMock;
    private $loggerMock;
    private $chargeService;

    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(SplitpagApi::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->chargeService = new ChargeService($this->apiMock, $this->loggerMock);
    }

    public function testGetCharges(): void
    {
        $expectedResult = ['charges' => []];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/charge', ['page' => '1'])
            ->willReturn($expectedResult);

        $result = $this->chargeService->getCharges('1');
        $this->assertSame($expectedResult, $result);
    }

    public function testGetChargeCreateData(): void
    {
        $expectedResult = ['data' => []];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/charge/create')
            ->willReturn($expectedResult);

        $result = $this->chargeService->getChargeCreateData();
        $this->assertSame($expectedResult, $result);
    }

    public function testCreateChargeSuccess(): void
    {
        $chargeData = [
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
            'phone' => '(123)456-7890',
            'type' => 'u',
            'leverage_days_single' => '30',
            'leverage_min_percent_single' => '10.0',
            'description_single_charge' => 'Test charge',
            'amount' => '100.00',
            'due_date_single_charge' => '2023-12-31'
        ];

        $expectedResult = ['success' => true, 'message' => 'Charge created successfully'];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('POST', '/charge/create', $chargeData)
            ->willReturn($expectedResult);

        $result = $this->chargeService->createCharge($chargeData);
        $this->assertSame($expectedResult, $result);
    }

    public function testCreateChargeMissingField(): void
    {
        $chargeData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $result = $this->chargeService->createCharge($chargeData);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('is required', $result['message']);
    }

    public function testGetChargeStatus(): void
    {
        $hashChargeId = 'abc123';
        $expectedResult = ['status' => 'paid'];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', "/charge/status/$hashChargeId")
            ->willReturn($expectedResult);

        $result = $this->chargeService->getChargeStatus($hashChargeId);
        $this->assertSame($expectedResult, $result);
    }
}
