<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Период частотного ограничения показов (`FrequencyRule::getPeriod()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class FrequencyPeriod extends AbstractEnum
{
    /** Без ограничения */
    public const NO_LIMIT = 'no_limit';

    /** День */
    public const DAY = 'day';

    /** Неделя */
    public const WEEK = 'week';

    /** Месяц */
    public const MONTH = 'month';
}
