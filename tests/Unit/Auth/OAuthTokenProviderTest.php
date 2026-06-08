<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit\Auth;

use AvitoAds\Auth\Credentials;
use AvitoAds\Auth\OAuthTokenProvider;
use AvitoAds\Auth\Storage\InMemoryTokenStorage;
use AvitoAds\Exception\AuthenticationException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class OAuthTokenProviderTest extends TestCase
{
    private function tokenBody(string $token, int $expiresIn = 86400): string
    {
        return (string) json_encode([
            'access_token' => $token,
            'expires_in' => $expiresIn,
            'token_type' => 'Bearer',
        ]);
    }

    private function provider(MockHandler $mock, InMemoryTokenStorage $storage): OAuthTokenProvider
    {
        $guzzle = new GuzzleClient(['handler' => HandlerStack::create($mock)]);

        return new OAuthTokenProvider(
            $guzzle,
            new Credentials('cid', 'secret'),
            'https://api.avito.ru/token',
            $storage
        );
    }

    public function testFetchesAndCachesToken(): void
    {
        $mock = new MockHandler([new Response(200, [], $this->tokenBody('tok-1'))]);
        $storage = new InMemoryTokenStorage();
        $provider = $this->provider($mock, $storage);

        self::assertSame('tok-1', $provider->getToken()->getToken());
        // Второй вызов берёт из кэша (очередь моков пуста — иначе была бы ошибка).
        self::assertSame('tok-1', $provider->getToken()->getToken());
    }

    public function testForgetTokenForcesRefetch(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->tokenBody('tok-1')),
            new Response(200, [], $this->tokenBody('tok-2')),
        ]);
        $storage = new InMemoryTokenStorage();
        $provider = $this->provider($mock, $storage);

        self::assertSame('tok-1', $provider->getToken()->getToken());
        $provider->forgetToken();
        self::assertSame('tok-2', $provider->getToken()->getToken());
    }

    public function testExpiredTokenIsRefetched(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->tokenBody('tok-1', 1)),
            new Response(200, [], $this->tokenBody('tok-2')),
        ]);
        $storage = new InMemoryTokenStorage();
        $provider = $this->provider($mock, $storage);

        // expires_in=1 и leeway по умолчанию 60 → токен сразу считается истёкшим.
        self::assertSame('tok-1', $provider->getToken()->getToken());
        self::assertSame('tok-2', $provider->getToken()->getToken());
    }

    public function testInvalidTokenResponseThrows(): void
    {
        $mock = new MockHandler([new Response(200, [], (string) json_encode(['foo' => 'bar']))]);
        $provider = $this->provider($mock, new InMemoryTokenStorage());

        $this->expectException(AuthenticationException::class);
        $provider->getToken();
    }

    public function testEmptyAccessTokenThrows(): void
    {
        $body = (string) json_encode(['access_token' => '', 'expires_in' => 86400, 'token_type' => 'Bearer']);
        $mock = new MockHandler([new Response(200, [], $body)]);
        $provider = $this->provider($mock, new InMemoryTokenStorage());

        $this->expectException(AuthenticationException::class);
        $provider->getToken();
    }

    public function testMissingExpiresInThrows(): void
    {
        $body = (string) json_encode(['access_token' => 'tok-1', 'token_type' => 'Bearer']);
        $mock = new MockHandler([new Response(200, [], $body)]);
        $provider = $this->provider($mock, new InMemoryTokenStorage());

        $this->expectException(AuthenticationException::class);
        $provider->getToken();
    }

    /**
     * @dataProvider nonPositiveExpiresInProvider
     * @param mixed $expiresIn
     */
    public function testNonPositiveExpiresInThrows($expiresIn): void
    {
        $body = (string) json_encode([
            'access_token' => 'tok-1',
            'expires_in' => $expiresIn,
            'token_type' => 'Bearer',
        ]);
        $mock = new MockHandler([new Response(200, [], $body)]);
        $provider = $this->provider($mock, new InMemoryTokenStorage());

        $this->expectException(AuthenticationException::class);
        $provider->getToken();
    }

    /**
     * @return array<string, array{mixed}>
     */
    public function nonPositiveExpiresInProvider(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
        ];
    }
}
