<?php

declare(strict_types=1);

namespace AvitoAds;

use AvitoAds\Auth\OAuthTokenProvider;
use AvitoAds\Auth\Storage\InMemoryTokenStorage;
use AvitoAds\Auth\Storage\Psr16TokenStorage;
use AvitoAds\Auth\Storage\TokenStorageInterface;
use AvitoAds\Auth\TokenProviderInterface;
use AvitoAds\Http\GuzzleHttpClient;
use AvitoAds\Http\HttpClientInterface;
use AvitoAds\Http\Middleware\AuthMiddleware;
use AvitoAds\Http\Middleware\RetryMiddleware;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;

/**
 * Сборщик низкоуровневых зависимостей клиента из конфигурации.
 *
 * Здесь определяется порядок middleware: повтор (retry) — внешний, авторизация —
 * внутренняя. Благодаря этому при повторе запроса после 401 заголовок
 * Authorization формируется заново уже со свежим токеном.
 */
final class ClientBuilder
{
    /**
     * Создаёт транспортный HTTP-клиент со всеми middleware.
     */
    public static function buildHttpClient(
        Configuration $config,
        TokenProviderInterface $tokenProvider
    ): HttpClientInterface {
        $stack = HandlerStack::create($config->getHttpHandler());

        // Повтор — внешний middleware (push первым → оборачивает остальные).
        $retry = new RetryMiddleware(
            $config->getMaxRetries(),
            $config->getRetryBaseDelayMs(),
            $tokenProvider,
            $config->getLogger()
        );
        $stack->push($retry->create(), 'avito_retry');

        // Авторизация — внутренний middleware (push вторым → ближе к обработчику).
        $stack->push(new AuthMiddleware($tokenProvider), 'avito_auth');

        $guzzle = new GuzzleClient([
            'base_uri' => $config->getBaseUri(),
            'handler' => $stack,
            'timeout' => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
        ]);

        return new GuzzleHttpClient($guzzle, $config->getLogger());
    }

    /**
     * Создаёт поставщик токена (с отдельным Guzzle-клиентом без авторизации).
     */
    public static function buildTokenProvider(Configuration $config): TokenProviderInterface
    {
        $tokenStack = HandlerStack::create($config->getHttpHandler());
        $tokenGuzzle = new GuzzleClient([
            'handler' => $tokenStack,
            'timeout' => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
        ]);

        return new OAuthTokenProvider(
            $tokenGuzzle,
            $config->getCredentials(),
            $config->getTokenUrl(),
            self::resolveStorage($config),
            $config->getTokenLeeway(),
            $config->getLogger()
        );
    }

    /**
     * Выбор хранилища токена: явное → PSR-16 кэш → в памяти.
     */
    private static function resolveStorage(Configuration $config): TokenStorageInterface
    {
        $explicit = $config->getTokenStorage();
        if ($explicit !== null) {
            return $explicit;
        }

        $cache = $config->getCache();
        if ($cache !== null) {
            return new Psr16TokenStorage($cache);
        }

        return new InMemoryTokenStorage();
    }
}
