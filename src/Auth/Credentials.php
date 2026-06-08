<?php

declare(strict_types=1);

namespace AvitoAds\Auth;

use AvitoAds\Exception\ConfigurationException;

/**
 * Учётные данные приложения для OAuth 2.0 (client credentials).
 *
 * Пара `client_id` / `client_secret` создаётся во вкладке **API** кабинета
 * Авито Реклама пользователем с ролью Администратора и привязана к конкретному
 * рекламному аккаунту.
 */
final class Credentials
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        if ($clientId === '' || $clientSecret === '') {
            throw new ConfigurationException('client_id и client_secret не могут быть пустыми.');
        }
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Ключ для кэширования токена — стабильный хеш от client_id.
     *
     * Секрет в ключ не включается.
     */
    public function getCacheKey(): string
    {
        return 'avito_ads_token_' . sha1($this->clientId);
    }
}
