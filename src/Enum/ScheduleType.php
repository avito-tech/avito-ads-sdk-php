<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип расписания показов группы (`Group::getScheduleType()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class ScheduleType extends AbstractEnum
{
    /** Всегда */
    public const ALWAYS = 'always';

    /** Будни с 9 до 20 */
    public const WEEKDAYS_9_TO_20 = 'weekdays9To20';

    /** Своё расписание (см. `Group::getActivitySchedule()`) */
    public const CUSTOM = 'custom';
}
