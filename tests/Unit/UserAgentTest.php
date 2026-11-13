<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Client;
use AvitoAds\Configuration;
use AvitoAds\Http\UserAgent;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class UserAgentTest extends TestCase
{
    public function testDefaultUserAgentIsSdkLabel(): void
    {
        self::assertSame('avito-ads-sdk-php/' . Client::VERSION, UserAgent::label());
        self::assertSame(UserAgent::label(), UserAgent::build());
        self::assertSame(UserAgent::label(), UserAgent::build(''));
        self::assertSame(UserAgent::label(), Configuration::create('id', 'secret', 1)->getUserAgent());
    }

    public function testCustomUserAgentIsAppendedWithLabel(): void
    {
        self::assertSame('my-app/1.0 avito-ads-sdk-php/1.4.0', UserAgent::build('my-app/1.0'));

        $config = Configuration::create('id', 'secret', 1)->withUserAgent('my-app/1.0');
        self::assertSame('my-app/1.0 avito-ads-sdk-php/1.4.0', $config->getUserAgent());
    }

    public function testLabelIsNotDuplicated(): void
    {
        self::assertSame('my-app/1.0 avito-ads-sdk-php/1.4.0', UserAgent::build('my-app/1.0 avito-ads-sdk-php/1.4.0'));
        // Без учёта регистра и версии.
        self::assertSame('my-app/1.0 Avito-Ads-SDK-PHP/0.9', UserAgent::build('my-app/1.0 Avito-Ads-SDK-PHP/0.9'));
    }

    public function testDefaultUserAgentIsSentToTokenAndApiRequests(): void
    {
        $requests = $this->callBalance(Configuration::create('id', 'secret', 123));

        self::assertCount(2, $requests);
        self::assertStringEndsWith('/token', (string) $requests[0]->getUri());
        self::assertSame('avito-ads-sdk-php/1.4.0', $requests[0]->getHeaderLine('User-Agent'));
        self::assertSame('avito-ads-sdk-php/1.4.0', $requests[1]->getHeaderLine('User-Agent'));
    }

    public function testCustomUserAgentIsSentToTokenAndApiRequests(): void
    {
        $requests = $this->callBalance(Configuration::create('id', 'secret', 123)->withUserAgent('my-app/1.0'));

        self::assertSame('my-app/1.0 avito-ads-sdk-php/1.4.0', $requests[0]->getHeaderLine('User-Agent'));
        self::assertSame('my-app/1.0 avito-ads-sdk-php/1.4.0', $requests[1]->getHeaderLine('User-Agent'));
    }

    /**
     * Выполняет getBalance() и возвращает отправленные запросы: [0] — токен, [1] — API.
     *
     * @return RequestInterface[]
     */
    private function callBalance(Configuration $config): array
    {
        $requests = [];
        $capture = static function (Response $response) use (&$requests): callable {
            return static function (RequestInterface $request) use (&$requests, $response): Response {
                $requests[] = $request;

                return $response;
            };
        };
        $token = (string) json_encode(['access_token' => 't', 'expires_in' => 3600, 'token_type' => 'Bearer']);
        $mock = new MockHandler([
            $capture(new Response(200, [], $token)),
            $capture(new Response(200, [], (string) json_encode(['balance' => 1, 'bonusBalance' => 0]))),
        ]);

        (new Client($config->withHttpHandler($mock)->withRetryBaseDelay(0)))->account()->getBalance();

        return $requests;
    }
}
