<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * Ошибка валидации входных данных на стороне клиента.
 *
 * Бросается до отправки запроса — например, при `amount < 1`,
 * периоде статистики больше 100 дней или `limit` вне диапазона 1–100.
 */
class ValidationException extends \InvalidArgumentException implements AvitoAdsException
{
}
