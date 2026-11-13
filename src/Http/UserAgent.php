<?php

declare(strict_types=1);

namespace AvitoAds\Http;

use AvitoAds\Client;

/**
 * Сборка заголовка User-Agent.
 *
 * По умолчанию SDK отправляет метку `avito-ads-sdk-php/<версия>`. Пользовательский
 * User-Agent метку не заменяет: SDK дописывает её через пробел
 * (`my-app/1.0 avito-ads-sdk-php/1.4.0`). Если метка уже есть в строке (без учёта
 * регистра), второй раз она не дописывается.
 *
 * Тот же User-Agent уходит и в запрос токена, и в обычные запросы к API.
 */
final class UserAgent
{
    /** Префикс метки SDK, по которому API определяет, что запрос пришёл из PHP SDK. */
    public const LABEL_PREFIX = 'avito-ads-sdk-php/';

    private function __construct()
    {
    }

    /**
     * Метка SDK: `avito-ads-sdk-php/<Client::VERSION>`.
     */
    public static function label(): string
    {
        return self::LABEL_PREFIX . Client::VERSION;
    }

    /**
     * Итоговый User-Agent: метка SDK или пользовательский UA с дописанной меткой.
     */
    public static function build(?string $userAgent = null): string
    {
        $userAgent = $userAgent === null ? '' : trim($userAgent);
        if ($userAgent === '') {
            return self::label();
        }

        if (stripos($userAgent, self::LABEL_PREFIX) !== false) {
            return $userAgent;
        }

        return $userAgent . ' ' . self::label();
    }
}
