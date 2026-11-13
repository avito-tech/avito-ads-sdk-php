<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Статус креатива.
 *
 * Значения в ответах API не проверяются: неизвестный статус возвращается строкой как есть.
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

    /** Ждёт запуска */
    public const WILL_LAUNCH_SOON = 'will_launch_soon';

    /** Активен */
    public const ACTIVE = 'active';

    /** Приостанавливается */
    public const PAUSING = 'pausing';

    /** Приостановлен */
    public const PAUSED = 'paused';

    /** Возобновляется */
    public const UNPAUSING = 'unpausing';

    /** Остановлен */
    public const STOPPED = 'stopped';

    /** Завершён */
    public const FINISHED = 'finished';

    /** В архиве */
    public const ARCHIVED = 'archived';
}
