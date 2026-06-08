<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Роль пользователя в рекламном кабинете.
 */
final class UserRole extends AbstractEnum
{
    /** Полный доступ ко всем функциям */
    public const ADMIN = 'admin';

    /** Только просмотр */
    public const VIEWER = 'viewer';
}
