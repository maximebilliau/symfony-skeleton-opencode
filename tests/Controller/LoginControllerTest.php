<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixturesTrait;
use App\Tests\Integration\Common\Infrastructure\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testLogin(): void
    {
        $this->loadFixtures();

        $response = $this->request(
            method: 'POST',
            uri: '/api/login',
            content: json_encode([
                'email' => 'admin@skeleton.com',
                'password' => 'admin',
            ]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($response->getContent() ?: '');
        /** @var array<string, mixed> $responseData */
        $responseData = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('token', $responseData);

        $this->request(
            method: 'POST',
            uri: '/api/login',
            content: json_encode([
                'email' => 'wrong@example.com',
                'password' => 'wrongpassword',
            ]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
