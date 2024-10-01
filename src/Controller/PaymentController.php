<?php

namespace App\Controller;

use App\Service\PaymentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getPayments(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? '1';
        $search = $queryParams['search'] ?? '';

        $payments = $this->paymentService->getPayments($page, $search);

        $response->getBody()->write(json_encode($payments));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function checkStatusPayment(Request $request, Response $response, array $args): Response
    {
        $hashPaymentId = $args['hash_payment_id'];

        $paymentStatus = $this->paymentService->checkStatusPayment($hashPaymentId);

        $response->getBody()->write(json_encode($paymentStatus));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
