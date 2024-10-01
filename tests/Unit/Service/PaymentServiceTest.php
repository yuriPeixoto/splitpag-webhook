<?php

namespace Tests\Unit\Service;

use App\Service\PaymentService;
use App\Api\SplitpagApi;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentServiceTest extends TestCase
{
    private $apiMock;
    private $loggerMock;
    private $paymentService;

    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(SplitpagApi::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->paymentService = new PaymentService($this->apiMock, $this->loggerMock);
    }

    public function testGetPayments(): void
    {
        $expectedResult = ['payments' => []];
        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/payment', ['page' => '1'])
            ->willReturn($expectedResult);

        $result = $this->paymentService->getPayments('1');
        $this->assertSame($expectedResult, $result);
    }

    public function testCheckStatusPayment(): void
    {
        $hashPaymentId = 'abc123';
        $expectedResult = [
            'hash_id' => '1-abc',
            'amount_original' => '10.00',
            'amount_paid' => '10.00',
            'amount_proposal' => '0.00',
            'paid_at' => '2023-01-01',
            'status' => 'paid',
            'method' => 'credit_card',
            'is_internacional' => false,
            'document_url' => 'https://document.pdf',
            'document_hash' => 'https://qr-code',
            'created_at' => '2023-01-01 00:00:00',
            'updated_at' => '2023-01-01 00:00:00'
        ];

        $this->apiMock->expects($this->once())
            ->method('makeRequest')
            ->with('GET', '/payment/checkStatusPayment', ['hash_payment_id' => $hashPaymentId])
            ->willReturn($expectedResult);

        $result = $this->paymentService->checkStatusPayment($hashPaymentId);
        $this->assertSame($expectedResult, $result);
    }
}
