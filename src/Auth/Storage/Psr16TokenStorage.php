<?php

declare(strict_types=1);

namespace AvitoAds\Auth\Storage;

use AvitoAds\Auth\AccessToken;
use Psr\SimpleCache\CacheInterface;

/**
 * Хранилище токена поверх PSR-16 кэша (Simple Cache).
 *
 * Позволяет переиспользовать один токен между разными процессами/запросами.
 * Подходит для интеграции с кэшем Laravel, Symfony и любым PSR-16 совместимым.
 */
final class Psr16TokenStorage implements TokenStorageInterface
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function get(string $key): ?AccessToken
    {
        /** @var array<string, mixed>|null $data */
        $data = $this->cache->get($key);
        if (!is_array($data)) {
            return null;
        }

        return AccessToken::fromArray($data);
    }

    public function set(string $key, AccessToken $token): void
    {
        // TTL равен оставшемуся сроку жизни токена (но не меньше 1 секунды).
        $ttl = max(1, $token->getExpiresAt() - time());
        $this->cache->set($key, $token->toArray(), $ttl);
    }

    public function delete(string $key): void
    {
        $this->cache->delete($key);
    }
}
