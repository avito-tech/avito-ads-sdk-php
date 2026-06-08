<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * HTTP 429 — превышен лимит запросов (по умолчанию 500 запросов в минуту).
 *
 * Клиент по умолчанию автоматически повторяет такие запросы с экспоненциальной
 * задержкой; это исключение бросается, только когда исчерпаны все попытки.
 */
class RateLimitException extends ApiException
{
    /**
     * Рекомендованное время ожидания в секундах (из заголовка `Retry-After`), если есть.
     */
    public function getRetryAfter(): ?int
    {
        $response = $this->getResponse();
        if ($response === null) {
            return null;
        }
        $header = $response->getHeaderLine('Retry-After');

        return $header !== '' && is_numeric($header) ? (int) $header : null;
    }
}
