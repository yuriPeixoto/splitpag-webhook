<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use App\Service\ClientService;
use App\Service\ChargeService;

class WebhookProcessor
{
    private $logger;
    private $clientService;
    private $chargeService;
    private $paymentService;

    public function __construct(
        LoggerInterface $logger,
        ClientService $clientService,
        ChargeService $chargeService,
        PaymentService $paymentService
    ) {
        $this->logger = $logger;
        $this->clientService = $clientService;
        $this->chargeService = $chargeService;
        $this->paymentService = $paymentService;
    }

    public function process(string $event, array $payload): void
    {
        $this->logger->info('Processing webhook event', ['event' => $event]);

        switch ($event) {
            case 'client.created':
                $this->processClientCreated($payload);
                break;
            case 'client.updated':
                $this->processClientUpdated($payload);
                break;
            case 'charge.created':
            case 'charge.updated':
                $this->processChargeEvent($event, $payload);
                break;
            case 'payment.created':
            case 'payment.updated':
            case 'payment.status_changed':
                $this->processPaymentEvent($event, $payload);
                break;
            default:
                $this->logger->warning('Unhandled webhook event type', ['event' => $event]);
                break;
        }
    }

    private function processClientCreated(array $payload): void
    {
        $clientId = $payload['client_id'] ?? null;
        if ($clientId) {
            // Fetch the newly created client details
            $clientData = $this->clientService->getClients('1', $clientId);
            if (!empty($clientData['data'])) {
                $client = $clientData['data'][0];
                // Process the new client data (e.g., save to your local database)
                $this->logger->info('New client created', ['client_id' => $clientId, 'client_name' => $client['name']]);
                // You might want to add more processing here
            } else {
                $this->logger->warning('Client created but not found', ['client_id' => $clientId]);
            }
        } else {
            $this->logger->error('Client created event missing client_id', ['payload' => $payload]);
        }
    }

    private function processClientUpdated(array $payload): void
    {
        $clientId = $payload['client_id'] ?? null;
        if ($clientId) {
            // Fetch the updated client details
            $clientData = $this->clientService->getClients('1', $clientId);
            if (!empty($clientData['data'])) {
                $client = $clientData['data'][0];
                // Process the updated client data (e.g., update your local database)
                $this->logger->info('Client updated', ['client_id' => $clientId, 'client_name' => $client['name']]);
                // You might want to add more processing here
            } else {
                $this->logger->warning('Client updated but not found', ['client_id' => $clientId]);
            }
        } else {
            $this->logger->error('Client updated event missing client_id', ['payload' => $payload]);
        }
    }

    private function processChargeEvent(string $event, array $payload): void
    {
        $chargeId = $payload['charge_id'] ?? null;
        if ($chargeId) {
            $chargeData = $this->chargeService->getChargeStatus($chargeId);
            
            switch ($event) {
                case 'charge.created':
                    $this->logger->info('New charge created', [
                        'charge_id' => $chargeId,
                        'type' => $chargeData['type'] ?? 'unknown',
                        'amount' => $chargeData['amount'] ?? 'unknown'
                    ]);
                    // Additional processing for new charges
                    break;
                
                case 'charge.updated':
                    $this->logger->info('Charge updated', [
                        'charge_id' => $chargeId,
                        'type' => $chargeData['type'] ?? 'unknown',
                        'amount' => $chargeData['amount'] ?? 'unknown'
                    ]);
                    // Additional processing for updated charges
                    break;
                
                case 'charge.status_changed':
                    $this->logger->info('Charge status changed', [
                        'charge_id' => $chargeId,
                        'new_status' => $chargeData['status'] ?? 'unknown'
                    ]);
                    // Additional processing for status changes
                    // For example, you might want to update your local database or notify the user
                    break;
            }
            
            // You can add more specific processing based on charge type (u, p, r) if needed
            // This information is available in $chargeData['type']
        } else {
            $this->logger->error('Charge event missing charge_id', ['payload' => $payload]);
        }
    }

    private function processPaymentEvent(string $event, array $payload): void
    {
        $paymentId = $payload['payment_id'] ?? null;
        
        if ($paymentId) {
            $paymentData = $this->paymentService->checkStatusPayment($paymentId);
            
            switch ($event) {
                case 'payment.created':
                    $this->logger->info('New payment created', [
                        'payment_id' => $paymentId,
                        'amount' => $paymentData['amount'] ?? 'unknown',
                        'status' => $paymentData['status'] ?? 'unknown'
                    ]);
                    // Additional processing for new payments
                    break;
                
                case 'payment.updated':
                    $this->logger->info('Payment updated', [
                        'payment_id' => $paymentId,
                        'amount' => $paymentData['amount'] ?? 'unknown',
                        'status' => $paymentData['status'] ?? 'unknown'
                    ]);
                    // Additional processing for updated payments
                    break;
                
                case 'payment.status_changed':
                    $this->logger->info('Payment status changed', [
                        'payment_id' => $paymentId,
                        'new_status' => $paymentData['status'] ?? 'unknown'
                    ]);
                    // Additional processing for status changes
                    // For example, you might want to update your local database or notify the user
                    break;
            }
            
            // You can add more specific processing based on payment status if needed
            // This information is available in $paymentData['status']
        } else {
            $this->logger->error('Payment event missing payment_id', ['payload' => $payload]);
        }
    }
}
