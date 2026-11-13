<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Статус кампании.
 *
 * Значения в ответах API не проверяются: неизвестный статус возвращается строкой как есть.
 */
final class CampaignStatus extends AbstractEnum
{
    /** Черновик */
    public const DRAFT = 'draft';

    /** На модерации */
    public const IN_MODERATION = 'in_moderation';

    /** Модерация не пройдена */
    public const MODERATION_FAILED = 'moderation_failed';

    /** Частичная модерация */
    public const PARTIAL_MODERATION = 'partial_moderation';

    /** Активна */
    public const ACTIVE = 'active';

    /** Запустится скоро */
    public const WILL_LAUNCH_SOON = 'will_launch_soon';

    /** Частично активна */
    public const PARTIALLY_ACTIVE = 'partially_active';

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
