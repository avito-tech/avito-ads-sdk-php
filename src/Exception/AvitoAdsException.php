<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * Базовый интерфейс для всех исключений библиотеки.
 *
 * Позволяет в коде приложения перехватывать любые ошибки клиента
 * одним `catch (AvitoAdsException $e)`.
 */
interface AvitoAdsException extends \Throwable
{
}
