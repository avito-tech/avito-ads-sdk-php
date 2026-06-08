<?php

declare(strict_types=1);

namespace AvitoAds\Integration\CodeIgniter;

use CodeIgniter\Config\BaseConfig;

/**
 * Конфигурация клиента Авито Реклама для CodeIgniter 4.
 *
 * Способ использования: скопируйте этот класс в app/Config/ своего проекта
 * (или унаследуйтесь от него) и задайте значения через переменные окружения
 * в файле .env с префиксом `avitoAds.` — CodeIgniter подставит их автоматически.
 *
 * Пример .env:
 *   avitoAds.clientId = "ваш_client_id"
 *   avitoAds.clientSecret = "ваш_client_secret"
 *   avitoAds.accountId = 123456789
 *   avitoAds.environment = "production"
 */
class AvitoAds extends BaseConfig
{
    /** Идентификатор приложения (client_id). */
    public string $clientId = '';

    /** Секрет приложения (client_secret). */
    public string $clientSecret = '';

    /** Идентификатор рекламного аккаунта. */
    public int $accountId = 0;

    /** Окружение: 'production' или 'sandbox'. */
    public string $environment = 'production';

    /** Таймаут запроса, секунды. */
    public float $timeout = 30.0;

    /** Таймаут соединения, секунды. */
    public float $connectTimeout = 10.0;

    /** Максимум повторов при 429/5xx/сетевых ошибках. */
    public int $maxRetries = 4;

    /** Базовая задержка повтора, мс. */
    public int $retryBaseDelayMs = 1000;

    /** Запас на досрочное обновление токена, секунды. */
    public int $tokenLeeway = 60;
}
