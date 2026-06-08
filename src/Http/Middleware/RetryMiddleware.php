<?php

declare(strict_types=1);

namespace AvitoAds\Http\Middleware;

use AvitoAds\Auth\TokenProviderInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Создаёт Guzzle-middleware для автоматического повтора запросов.
 *
 * Повторяет при:
 *  - сетевых ошибках (ConnectException);
 *  - HTTP 429 (превышен лимит) — с экспоненциальной задержкой 1 → 2 → 4 → 8 с;
 *  - HTTP 500/503 — с той же задержкой;
 *  - HTTP 401 — один раз (только если это первый ответ), предварительно сбросив
 *    кэшированный токен, чтобы следующая попытка получила свежий токен (без задержки).
 *
 * Состояние числа попыток Guzzle хранит per-request в $options['retries'],
 * поэтому middleware безопасно переиспользовать между запросами на одном клиенте.
 */
final class RetryMiddleware
{
    /** @var int Максимум повторов для 429/5xx/сетевых ошибок. */
    private $maxRetries;

    /** @var int Базовая задержка в миллисекундах (для экспоненты). */
    private $baseDelayMs;

    /** @var TokenProviderInterface|null */
    private $tokenProvider;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        int $maxRetries = 4,
        int $baseDelayMs = 1000,
        ?TokenProviderInterface $tokenProvider = null,
        ?LoggerInterface $logger = null
    ) {
        $this->maxRetries = $maxRetries;
        $this->baseDelayMs = $baseDelayMs;
        $this->tokenProvider = $tokenProvider;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Возвращает готовый Guzzle-middleware.
     *
     * @return callable
     */
    public function create(): callable
    {
        return Middleware::retry($this->decider(), $this->delay());
    }

    /**
     * @return callable
     */
    private function decider(): callable
    {
        $maxRetries = $this->maxRetries;
        $provider = $this->tokenProvider;
        $logger = $this->logger;

        return static function (
            int $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null,
            ?\Throwable $exception = null
        ) use (
            $maxRetries,
            $provider,
            $logger
        ): bool {
            // Сетевые ошибки — повторяем в пределах лимита.
            if ($exception instanceof ConnectException) {
                return $retries < $maxRetries;
            }

            if ($response === null) {
                return false;
            }

            $status = $response->getStatusCode();

            // 401 на первом же ответе — однократно обновляем токен и повторяем.
            if ($status === 401 && $provider !== null && $retries === 0) {
                $provider->forgetToken();
                $logger->debug('Avito Ads: получен 401, токен сброшен, повтор запроса.');

                return true;
            }

            // 429 и 5xx — повторяем с экспоненциальной задержкой в пределах лимита.
            if (($status === 429 || $status >= 500) && $retries < $maxRetries) {
                $logger->debug(sprintf('Avito Ads: повтор после HTTP %d (попытка %d).', $status, $retries + 1));

                return true;
            }

            return false;
        };
    }

    /**
     * @return callable
     */
    private function delay(): callable
    {
        $baseDelayMs = $this->baseDelayMs;

        return static function (int $retries, ?ResponseInterface $response = null) use ($baseDelayMs): int {
            // Повтор из-за 401 выполняем мгновенно.
            if ($response !== null && $response->getStatusCode() === 401) {
                return 0;
            }

            // Уважаем заголовок Retry-After, если он задан (в секундах).
            if ($response !== null) {
                $retryAfter = $response->getHeaderLine('Retry-After');
                if ($retryAfter !== '' && is_numeric($retryAfter)) {
                    return (int) $retryAfter * 1000;
                }
            }

            // Экспоненциальная задержка: base * 2^(retries-1) → 1000, 2000, 4000, 8000 мс.
            $exponent = $retries > 0 ? $retries - 1 : 0;

            return (int) ($baseDelayMs * (2 ** $exponent));
        };
    }
}
