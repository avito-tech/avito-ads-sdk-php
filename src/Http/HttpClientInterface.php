<?php

declare(strict_types=1);

namespace AvitoAds\Http;

/**
 * Абстракция транспортного слоя.
 *
 * Скрывает детали HTTP-клиента (по умолчанию Guzzle), чтобы при необходимости
 * его можно было заменить, не трогая ресурсы и модели.
 */
interface HttpClientInterface
{
    /**
     * Выполняет запрос к API и возвращает унифицированный ответ.
     *
     * Путь указывается относительно базового URL окружения
     * (например, `v1/account/123/balance`). Авторизация, повторы и разбор
     * ошибок выполняются реализацией.
     *
     * @param string                    $method HTTP-метод (GET, POST, DELETE...)
     * @param string                    $path   относительный путь без базового URL
     * @param array<string, mixed>|null $body   тело запроса (будет отправлено как JSON)
     *
     * @throws \AvitoAds\Exception\AvitoAdsException
     */
    public function request(string $method, string $path, ?array $body = null): ApiResponse;
}
