<?php

declare(strict_types=1);

/*
 * Конфигурация клиента Авито Реклама для Laravel.
 *
 * Опубликуйте файл командой:
 *   php artisan vendor:publish --tag=avito-ads-config
 *
 * и задайте переменные окружения в .env (см. префикс AVITO_ADS_).
 */

return [
    // Идентификатор приложения (client_id) из вкладки «API» кабинета.
    'client_id' => env('AVITO_ADS_CLIENT_ID', ''),

    // Секрет приложения (client_secret).
    'client_secret' => env('AVITO_ADS_CLIENT_SECRET', ''),

    // Идентификатор рекламного аккаунта (path-параметр accountID).
    'account_id' => (int) env('AVITO_ADS_ACCOUNT_ID', 0),

    // Окружение: 'production' или 'sandbox'.
    'environment' => env('AVITO_ADS_ENVIRONMENT', 'production'),

    // Таймауты, секунды.
    'timeout' => (float) env('AVITO_ADS_TIMEOUT', 30),
    'connect_timeout' => (float) env('AVITO_ADS_CONNECT_TIMEOUT', 10),

    // Повторы при 429/5xx/сетевых ошибках.
    'max_retries' => (int) env('AVITO_ADS_MAX_RETRIES', 4),
    'retry_base_delay_ms' => (int) env('AVITO_ADS_RETRY_BASE_DELAY_MS', 1000),

    // Запас в секундах для досрочного обновления токена.
    'token_leeway' => (int) env('AVITO_ADS_TOKEN_LEEWAY', 60),

    // Использовать стандартный кэш Laravel (PSR-16) для хранения токена.
    'use_cache' => (bool) env('AVITO_ADS_USE_CACHE', true),
];
