<?php

declare(strict_types=1);

namespace AvitoAds\Auth;

/**
 * Поставщик access token.
 *
 * Абстрагирует получение и обновление токена, чтобы HTTP-слой не знал деталей
 * авторизации. Стандартная реализация — OAuthTokenProvider (grant client_credentials).
 */
interface TokenProviderInterface
{
    /**
     * Возвращает действующий токен, при необходимости получая/обновляя его.
     *
     * @throws \AvitoAds\Exception\AuthenticationException при невозможности получить токен
     */
    public function getToken(): AccessToken;

    /**
     * Принудительно сбрасывает кэшированный токен.
     *
     * Вызывается, например, при получении 401, чтобы следующий вызов getToken()
     * запросил новый токен.
     */
    public function forgetToken(): void;
}
