<?php

declare(strict_types=1);

namespace AvitoAds\Tests;

use AvitoAds\Client;
use AvitoAds\Configuration;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Базовый класс тестов с хелперами для мока HTTP через Guzzle MockHandler.
 */
abstract class TestCase extends BaseTestCase
{
    /** @var MockHandler|null Последний созданный обработчик (для проверки запросов). */
    protected $mockHandler;

    /**
     * Создаёт клиент с очередью замоканных ответов.
     *
     * Внимание: первый запрос в любом сценарии — получение токена, поэтому
     * первым в очередь обычно ставится ответ tokenResponse().
     *
     * @param Response[] $responses
     */
    protected function makeClient(array $responses, int $accountId = 123456789): Client
    {
        $mock = new MockHandler($responses);
        $this->mockHandler = $mock;
        $config = Configuration::create('test_client_id', 'test_client_secret', $accountId)
            ->withHttpHandler($mock)
            // Без задержек между повторами в тестах.
            ->withRetryBaseDelay(0);

        return new Client($config);
    }

    /**
     * Ответ эндпоинта /token.
     */
    protected function tokenResponse(string $token = 'test-token', int $expiresIn = 86400): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'access_token' => $token,
            'expires_in' => $expiresIn,
            'token_type' => 'Bearer',
        ]));
    }

    /**
     * JSON-ответ API.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function jsonResponse(int $status, array $data, array $headers = []): Response
    {
        $headers['Content-Type'] = 'application/json';

        return new Response($status, $headers, (string) json_encode($data));
    }
}
