<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Статус кампании.
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

    /** Приостановлена */
    public const PAUSED = 'paused';

    /** Остановлена */
    public const STOPPED = 'stopped';

    /** Завершена */
    public const FINISHED = 'finished';

    /** В архиве */
    public const ARCHIVED = 'archived';
}
