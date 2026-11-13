<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit\Http;

use AvitoAds\Exception\AccessDeniedException;
use AvitoAds\Exception\AuthenticationException;
use AvitoAds\Exception\BadRequestException;
use AvitoAds\Exception\ConflictException;
use AvitoAds\Exception\InvalidResponseException;
use AvitoAds\Exception\NetworkException;
use AvitoAds\Exception\NotFoundException;
use AvitoAds\Exception\RateLimitException;
use AvitoAds\Exception\ServerException;
use AvitoAds\Http\GuzzleHttpClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class GuzzleHttpClientTest extends TestCase
{
    private function client(MockHandler $mock): GuzzleHttpClient
    {
        $guzzle = new GuzzleClient([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://api.avito.ru/ads/',
        ]);

        return new GuzzleHttpClient($guzzle);
    }

    public function testSuccessParsesBodyAndPointBalance(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Api-Point-Balance' => '4231'], (string) json_encode(['balance' => 100])),
        ]);
        $response = $this->client($mock)->request('GET', 'v1/account/1/balance');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['balance' => 100], $response->getData());
        self::assertSame(4231, $response->getApiPointBalance());
    }

    /**
     * @dataProvider errorProvider
     * @param class-string<\Throwable> $expected
     */
    public function testErrorStatusMapsToException(int $status, string $expected): void
    {
        $mock = new MockHandler([
            new Response($status, [], (string) json_encode(['code' => 'err', 'message' => 'Ошибка'])),
        ]);
        $this->expectException($expected);
        $this->client($mock)->request('GET', 'v1/account/1');
    }

    /**
     * @return array<string, array{int, class-string}>
     */
    public function errorProvider(): array
    {
        return [
            '400' => [400, BadRequestException::class],
            '401' => [401, AuthenticationException::class],
            '403' => [403, AccessDeniedException::class],
            '404' => [404, NotFoundException::class],
            '409' => [409, ConflictException::class],
            '429' => [429, RateLimitException::class],
            '500' => [500, ServerException::class],
            '503' => [503, ServerException::class],
        ];
    }

    public function testErrorCarriesCodeAndMessage(): void
    {
        $mock = new MockHandler([
            new Response(400, [], (string) json_encode(['code' => 'bad_request', 'message' => 'Плохой запрос'])),
        ]);
        try {
            $this->client($mock)->request('POST', 'v1/account/1/funds-transfer', ['amount' => 5]);
            self::fail('Ожидалось исключение.');
        } catch (BadRequestException $e) {
            self::assertSame('bad_request', $e->getErrorCode());
            self::assertSame(400, $e->getStatusCode());
            self::assertSame('Плохой запрос', $e->getMessage());
        }
    }

    public function testRateLimitRetryAfter(): void
    {
        $mock = new MockHandler([
            new Response(429, ['Retry-After' => '7'], (string) json_encode(['message' => 'too many'])),
        ]);
        try {
            $this->client($mock)->request('GET', 'v1/account/1');
            self::fail('Ожидалось исключение.');
        } catch (RateLimitException $e) {
            self::assertSame(7, $e->getRetryAfter());
        }
    }

    public function testConnectExceptionBecomesNetworkException(): void
    {
        $mock = new MockHandler([
            new ConnectException('timeout', new Request('GET', 'v1/account/1')),
        ]);
        $this->expectException(NetworkException::class);
        $this->client($mock)->request('GET', 'v1/account/1');
    }

    public function testSuccessWithInvalidJsonThrows(): void
    {
        $mock = new MockHandler([new Response(200, [], '{not json')]);
        $this->expectException(InvalidResponseException::class);
        $this->client($mock)->request('GET', 'v1/account/1');
    }

    /**
     * @dataProvider scalarJsonProvider
     */
    public function testSuccessWithScalarJsonThrows(string $rawBody): void
    {
        $mock = new MockHandler([new Response(200, [], $rawBody)]);
        $this->expectException(InvalidResponseException::class);
        $this->client($mock)->request('GET', 'v1/account/1');
    }

    /**
     * @return array<string, array{string}>
     */
    public function scalarJsonProvider(): array
    {
        return [
            'integer' => ['123'],
            'boolean' => ['true'],
            'null' => ['null'],
            'string' => ['"oops"'],
        ];
    }

    public function testSuccessWithEmptyBodyReturnsEmptyData(): void
    {
        $mock = new MockHandler([new Response(204, [], '')]);
        $response = $this->client($mock)->request('GET', 'v1/account/1');

        self::assertSame(204, $response->getStatusCode());
        self::assertSame([], $response->getData());
    }

    public function testErrorWithMalformedBodyStillMapsToStatusException(): void
    {
        $mock = new MockHandler([new Response(500, [], '{broken')]);
        $this->expectException(ServerException::class);
        $this->client($mock)->request('GET', 'v1/account/1');
    }
}
