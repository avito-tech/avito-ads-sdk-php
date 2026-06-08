<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Статус группы объявлений.
 */
final class GroupsStatus extends AbstractEnum
{
    /** Черновик */
    public const DRAFT = 'draft';

    /** На модерации */
    public const IN_MODERATION = 'in_moderation';

    /** Модерация не пройдена */
    public const MODERATION_FAILED = 'moderation_failed';

    /** Запустится скоро */
    public const WILL_LAUNCH_SOON = 'will_launch_soon';

    /** Активна */
    public const ACTIVE = 'active';

    /** Остановится скоро */
    public const WILL_STOP_SOON = 'will_stop_soon';

    /** Приостанавливается */
    public const PAUSING = 'pausing';

    /** Приостановлена */
    public const PAUSED = 'paused';

    /** Возобновляется */
    public const UNPAUSING = 'unpausing';

    /** Остановлена */
    public const STOPPED = 'stopped';

    /** Завершена */
    public const FINISHED = 'finished';

    /** В архиве */
    public const ARCHIVED = 'archived';
}
