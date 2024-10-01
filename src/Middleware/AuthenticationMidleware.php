<?php

namespace App\Middleware;

use App\Service\AuthenticationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthenticationMiddleware
{
    private $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        if (empty($token)) {
            return new Response(401);
        }

        $token = str_replace('Bearer ', '', $token);
        if (!$this->authService->validateToken($token)) {
            return new Response(401);
        }

        return $handler->handle($request);
    }
}
