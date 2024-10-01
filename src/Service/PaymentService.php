<?php

namespace App\Service;

use App\Api\SplitpagApi;
use Psr\Log\LoggerInterface;

class PaymentService
{
    private $api;
    private $logger;

    public function __construct(SplitpagApi $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function getPayments(string $page, string $search = ''): array
    {
        $params = ['page' => $page];
        if (!empty($search)) {
            $params['search'] = $search;
        }

        return $this->api->makeRequest('GET', '/payment', $params);
    }

    public function checkStatusPayment(string $hashPaymentId): array
    {
        return $this->api->makeRequest('GET', "/payment/checkStatusPayment", ['hash_payment_id' => $hashPaymentId]);
    }
}
