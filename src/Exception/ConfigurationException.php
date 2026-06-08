<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * Ошибка конфигурации клиента (например, не заданы client_id/secret или accountID).
 *
 * Бросается до отправки запроса.
 */
class ConfigurationException extends \InvalidArgumentException implements AvitoAdsException
{
}
