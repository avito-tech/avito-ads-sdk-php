<?php

declare(strict_types=1);

namespace AvitoAds;

use AvitoAds\Auth\Credentials;
use AvitoAds\Auth\Storage\TokenStorageInterface;
use AvitoAds\Exception\ConfigurationException;
use AvitoAds\Http\UserAgent;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Конфигурация клиента Авито Реклама.
 *
 * Содержит учётные данные, идентификатор аккаунта (токен всегда привязан к одному
 * аккаунту), выбор окружения (прод/песочница), таймауты, параметры повторов,
 * логгер и кэш для хранения токена.
 *
 * Объект иммутабельный: методы `with*()` возвращают новый экземпляр.
 */
final class Configuration
{
    public const ENV_PRODUCTION = 'production';
    public const ENV_SANDBOX = 'sandbox';

    private const HOST = 'https://api.avito.ru';
    private const PREFIX_PRODUCTION = 'ads';
    private const PREFIX_SANDBOX = 'ads-sandbox';

    /** @var Credentials */
    private $credentials;

    /** @var int Идентификатор рекламного аккаунта (path-параметр accountID). */
    private $accountId;

    /** @var string */
    private $environment = self::ENV_PRODUCTION;

    /** @var float Таймаут запроса в секундах. */
    private $timeout = 30.0;

    /** @var float Таймаут соединения в секундах. */
    private $connectTimeout = 10.0;

    /** @var int Максимум повторов при 429/5xx/сетевых ошибках. */
    private $maxRetries = 4;

    /** @var int Базовая задержка повтора в миллисекундах. */
    private $retryBaseDelayMs = 1000;

    /** @var int Запас в секундах для досрочного обновления токена. */
    private $tokenLeeway = 60;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var CacheInterface|null PSR-16 кэш для хранения токена между запросами. */
    private $cache;

    /** @var TokenStorageInterface|null Явное хранилище токена (приоритетнее cache). */
    private $tokenStorage;

    /** @var callable|null Кастомный Guzzle-handler (для тестов/моков). */
    private $httpHandler;

    /** @var string|null Пользовательский User-Agent (метка SDK дописывается к нему). */
    private $userAgent;

    public function __construct(Credentials $credentials, int $accountId)
    {
        if ($accountId <= 0) {
            throw new ConfigurationException('accountID должен быть положительным целым числом.');
        }
        $this->credentials = $credentials;
        $this->accountId = $accountId;
    }

    /**
     * Быстрое создание конфигурации.
     */
    public static function create(string $clientId, string $clientSecret, int $accountId): self
    {
        return new self(new Credentials($clientId, $clientSecret), $accountId);
    }

    /**
     * @return static
     */
    public function production(): self
    {
        $clone = clone $this;
        $clone->environment = self::ENV_PRODUCTION;

        return $clone;
    }

    /**
     * @return static
     */
    public function sandbox(): self
    {
        $clone = clone $this;
        $clone->environment = self::ENV_SANDBOX;

        return $clone;
    }

    /**
     * @return static
     */
    public function withTimeout(float $seconds): self
    {
        $clone = clone $this;
        $clone->timeout = $seconds;

        return $clone;
    }

    /**
     * @return static
     */
    public function withConnectTimeout(float $seconds): self
    {
        $clone = clone $this;
        $clone->connectTimeout = $seconds;

        return $clone;
    }

    /**
     * @return static
     */
    public function withMaxRetries(int $maxRetries): self
    {
        $clone = clone $this;
        $clone->maxRetries = max(0, $maxRetries);

        return $clone;
    }

    /**
     * @return static
     */
    public function withRetryBaseDelay(int $milliseconds): self
    {
        $clone = clone $this;
        $clone->retryBaseDelayMs = max(0, $milliseconds);

        return $clone;
    }

    /**
     * @return static
     */
    public function withTokenLeeway(int $seconds): self
    {
        $clone = clone $this;
        $clone->tokenLeeway = max(0, $seconds);

        return $clone;
    }

    /**
     * @return static
     */
    public function withLogger(LoggerInterface $logger): self
    {
        $clone = clone $this;
        $clone->logger = $logger;

        return $clone;
    }

    /**
     * @return static
     */
    public function withCache(CacheInterface $cache): self
    {
        $clone = clone $this;
        $clone->cache = $cache;

        return $clone;
    }

    /**
     * @return static
     */
    public function withTokenStorage(TokenStorageInterface $storage): self
    {
        $clone = clone $this;
        $clone->tokenStorage = $storage;

        return $clone;
    }

    /**
     * Подменяет Guzzle-handler (например, MockHandler в тестах).
     *
     * @return static
     */
    public function withHttpHandler(callable $handler): self
    {
        $clone = clone $this;
        $clone->httpHandler = $handler;

        return $clone;
    }

    /**
     * Задаёт собственный User-Agent приложения.
     *
     * Метка SDK не заменяется, а дописывается через пробел:
     * `my-app/1.0` → `my-app/1.0 avito-ads-sdk-php/1.4.0`. Если строка уже содержит
     * `avito-ads-sdk-php/`, она отправляется как есть.
     *
     * @return static
     */
    public function withUserAgent(string $userAgent): self
    {
        $clone = clone $this;
        $clone->userAgent = $userAgent;

        return $clone;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isSandbox(): bool
    {
        return $this->environment === self::ENV_SANDBOX;
    }

    /**
     * Базовый URL API с учётом окружения, со слэшем на конце.
     *
     * Прод:      https://api.avito.ru/ads/
     * Песочница: https://api.avito.ru/ads-sandbox/
     */
    public function getBaseUri(): string
    {
        $prefix = $this->isSandbox() ? self::PREFIX_SANDBOX : self::PREFIX_PRODUCTION;

        return self::HOST . '/' . $prefix . '/';
    }

    /**
     * URL эндпоинта получения токена (не зависит от окружения).
     */
    public function getTokenUrl(): string
    {
        return self::HOST . '/token';
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getRetryBaseDelayMs(): int
    {
        return $this->retryBaseDelayMs;
    }

    public function getTokenLeeway(): int
    {
        return $this->tokenLeeway;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    public function getTokenStorage(): ?TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    /**
     * Итоговый User-Agent, который уходит в запрос токена и в запросы к API.
     */
    public function getUserAgent(): string
    {
        return UserAgent::build($this->userAgent);
    }

    public function getHttpHandler(): ?callable
    {
        return $this->httpHandler;
    }
}
