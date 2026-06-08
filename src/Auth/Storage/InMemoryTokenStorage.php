<?php

declare(strict_types=1);

namespace AvitoAds\Auth\Storage;

use AvitoAds\Auth\AccessToken;

/**
 * Хранилище токена в памяти текущего процесса.
 *
 * Используется по умолчанию. Подходит для CLI-скриптов и воркеров;
 * в типовом веб-запросе токен будет запрашиваться заново на каждый процесс.
 */
final class InMemoryTokenStorage implements TokenStorageInterface
{
    /** @var array<string, AccessToken> */
    private $tokens = [];

    public function get(string $key): ?AccessToken
    {
        return $this->tokens[$key] ?? null;
    }

    public function set(string $key, AccessToken $token): void
    {
        $this->tokens[$key] = $token;
    }

    public function delete(string $key): void
    {
        unset($this->tokens[$key]);
    }
}
