<?php

namespace Tests\Integration;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function testUnauthenticatedRequestFails()
    {
        $response = $this->get('/client');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testAuthenticatedRequestSucceeds()
    {
        $token = $this->getValidToken(); // Implement this method to get a valid token
        $response = $this->get('/client', ['Authorization' => 'Bearer ' . $token]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    // Add similar tests for other routes
}
