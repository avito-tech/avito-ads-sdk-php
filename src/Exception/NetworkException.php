<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * Сетевая ошибка: соединение не установлено, таймаут, DNS и т. п.
 *
 * Ответ от сервера получить не удалось.
 */
class NetworkException extends ApiException
{
}
