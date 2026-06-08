<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Статус креатива.
 */
final class CreativesStatus extends AbstractEnum
{
    /** Черновик */
    public const DRAFT = 'draft';

    /** Готов к модерации */
    public const READY_FOR_MODERATION = 'ready_for_moderation';

    /** На модерации */
    public const IN_MODERATION = 'in_moderation';

    /** Модерация не пройдена */
    public const MODERATION_FAILED = 'moderation_failed';

    /** Регистрация в ЕРИР */
    public const ERIR_REGISTRATION = 'erir_registration';

    /** Активен */
    public const ACTIVE = 'active';

    /** Приостановлен */
    public const PAUSED = 'paused';

    /** Остановлен */
    public const STOPPED = 'stopped';

    /** Завершён */
    public const FINISHED = 'finished';

    /** В архиве */
    public const ARCHIVED = 'archived';
}
