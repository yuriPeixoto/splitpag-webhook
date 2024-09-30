<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Service\AuthenticationService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Set up logger
$logger = new Logger('login-test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/login-test.log', Logger::DEBUG));

// Create AuthenticationService
$authService = new AuthenticationService($logger);

try {
    $token = $authService->getToken();
    echo "Login successful. Token: " . substr($token, 0, 10) . "...\n";
    $logger->info("Login successful. Token received.");
} catch (\Exception $e) {
    echo "Login failed: " . $e->getMessage() . "\n";
    $logger->error("Login failed: " . $e->getMessage());
}
