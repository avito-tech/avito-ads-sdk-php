<?php

declare(strict_types=1);

namespace AvitoAds\Auth\Storage;

use AvitoAds\Auth\AccessToken;

/**
 * Хранилище access token между запросами.
 *
 * Реализация по умолчанию (InMemoryTokenStorage) держит токен в памяти процесса.
 * Для веб-приложений рекомендуется Psr16TokenStorage, чтобы переиспользовать
 * токен между разными HTTP-запросами и не выбирать лимит на /token.
 */
interface TokenStorageInterface
{
    /**
     * Возвращает сохранённый токен по ключу или null, если его нет.
     */
    public function get(string $key): ?AccessToken;

    /**
     * Сохраняет токен под ключом.
     */
    public function set(string $key, AccessToken $token): void;

    /**
     * Удаляет токен (например, после получения 401).
     */
    public function delete(string $key): void;
}
