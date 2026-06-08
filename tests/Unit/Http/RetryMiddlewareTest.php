<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit\Http;

use AvitoAds\Tests\TestCase;

/**
 * Проверяет поведение повторов через полный стек клиента (с MockHandler).
 *
 * Порядок ответов в очереди учитывает, что и запрос токена, и запрос к API
 * идут через один и тот же MockHandler.
 */
final class RetryMiddlewareTest extends TestCase
{
    public function testRefreshesTokenAndRetriesOn401(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse('tok-1'),                 // первичный токен
            $this->jsonResponse(401, ['message' => 'expired']), // первая попытка API
            $this->tokenResponse('tok-2'),                 // повторное получение токена
            $this->jsonResponse(200, ['balance' => 777, 'bonusBalance' => 0]), // успех
        ]);

        $balance = $client->account()->getBalance();
        self::assertSame(777, $balance->getBalance());
        // Вся очередь должна быть израсходована (4 ответа).
        self::assertSame(0, $this->mockHandler->count());
        // Последняя попытка ушла уже с новым токеном.
        self::assertSame('Bearer tok-2', $this->mockHandler->getLastRequest()->getHeaderLine('Authorization'));
    }

    public function testRetriesOn429ThenSucceeds(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse('tok-1'),
            $this->jsonResponse(429, ['message' => 'slow down']),
            $this->jsonResponse(200, ['balance' => 10, 'bonusBalance' => 0]),
        ]);

        $balance = $client->account()->getBalance();
        self::assertSame(10, $balance->getBalance());
        self::assertSame(0, $this->mockHandler->count());
    }

    public function testRetriesOn503ThenSucceeds(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse('tok-1'),
            $this->jsonResponse(503, ['message' => 'unavailable']),
            $this->jsonResponse(200, ['balance' => 10, 'bonusBalance' => 0]),
        ]);

        self::assertSame(10, $client->account()->getBalance()->getBalance());
    }
}
