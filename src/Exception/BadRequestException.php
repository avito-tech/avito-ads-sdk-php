<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * HTTP 400 — некорректное тело запроса.
 *
 * Например: `amount < 1`, пустые обязательные поля, неверный формат дат,
 * период статистики больше 100 дней.
 */
class BadRequestException extends ApiException
{
}
