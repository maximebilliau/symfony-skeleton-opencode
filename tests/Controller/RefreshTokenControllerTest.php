<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixturesTrait;
use App\Tests\Integration\Common\Infrastructure\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testRefreshTokenWithValidToken(): void
    {
        $this->loadFixtures();

        // First, get a valid token by logging in
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
        /** @var array<string, mixed> $loginResponse */
        $loginResponse = json_decode($response->getContent() ?: '', true);
        $token = $loginResponse['token'] ?? null;

        $this->assertNotNull($token, 'Token should be returned from login');
        $this->assertIsString($token, 'Token should be a string');

        // Now test the refresh endpoint with the valid token
        $response = $this->request(
            method: 'POST',
            uri: '/api/token/refresh',
            content: json_encode([
                'token' => $token,
            ]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        /** @var array<string, mixed> $refreshResponse */
        $refreshResponse = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('token', $refreshResponse);
        $this->assertNotEmpty($refreshResponse['token']);
    }

    public function testRefreshTokenWithInvalidToken(): void
    {
        $response = $this->request(
            method: 'POST',
            uri: '/api/token/refresh',
            content: json_encode([
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30',
            ]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        /** @var array<string, mixed> $responseData */
        $responseData = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testRefreshTokenWithMissingToken(): void
    {
        $response = $this->request(
            method: 'POST',
            uri: '/api/token/refresh',
            content: json_encode([]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var array<string, mixed> $responseContent */
        $responseContent = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('message', $responseContent);
        $message = $responseContent['message'] ?? '';
        if (is_string($message)) {
            $this->assertStringContainsString('Token is required', $message);
        }
    }

    public function testRefreshTokenWithEmptyToken(): void
    {
        $response = $this->request(
            method: 'POST',
            uri: '/api/token/refresh',
            content: json_encode([
                'token' => '',
            ]) ?: '',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var array<string, mixed> $responseContent */
        $responseContent = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('message', $responseContent);
        $message = $responseContent['message'] ?? '';
        if (is_string($message)) {
            $this->assertStringContainsString('non-empty string', $message);
        }
    }

    public function testRefreshTokenWithInvalidJson(): void
    {
        $response = $this->request(
            method: 'POST',
            uri: '/api/token/refresh',
            content: 'invalid json',
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var array<string, mixed> $responseContent */
        $responseContent = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('message', $responseContent);
        $message = $responseContent['message'] ?? '';
        if (is_string($message)) {
            $this->assertStringContainsString('Invalid JSON', $message);
        }
    }
}
