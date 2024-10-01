<?php

namespace App\Handler;

use App\Service\AuthenticationService;
use App\Service\WebhookProcessor;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface;

class WebhookHandler
{
    private $authService;
    private $webhookProcessor;
    private $logger;
    private $responseFactory;

    public function __construct(
        AuthenticationService $authService,
        WebhookProcessor $webhookProcessor,
        LoggerInterface $logger,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->authService = $authService;
        $this->webhookProcessor = $webhookProcessor;
        $this->logger = $logger;
        $this->responseFactory = $responseFactory;
    }

    public function handle(Request $request): Response
    {
        try {
            $this->validateWebhook($request);
            $payload = $this->getPayload($request);
            $this->processWebhook($payload);

            return $this->responseFactory->createResponse(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream('{"message": "Webhook processed successfully"}'));
        } catch (\Exception $e) {
            $this->logger->error('Webhook processing failed: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => (string) $request->getBody(),
            ]);

            return $this->responseFactory->createResponse(400)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->createStream('{"error": "Webhook processing failed"}'));
        }
    }

    private function validateWebhook(Request $request): void
    {
        $token = $request->getHeaderLine('X-Splitpag-Token');
        if (empty($token) || !$this->authService->validateToken($token)) {
            throw new \Exception('Invalid or missing Splitpag token');
        }
    }

    private function getPayload(Request $request): array
    {
        $payload = json_decode((string) $request->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON payload');
        }
        return $payload;
    }

    private function processWebhook(array $payload): void
    {
        if (!isset($payload['event'])) {
            throw new \Exception('Missing event type in payload');
        }

        $this->webhookProcessor->process($payload['event'], $payload);
    }

    private function createStream(string $content): \Psr\Http\Message\StreamInterface
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        return new \GuzzleHttp\Psr7\Stream($stream);
    }
}
