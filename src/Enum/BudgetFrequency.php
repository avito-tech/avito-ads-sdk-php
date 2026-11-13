<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Период бюджета группы (`Group::getBudgetFrequency()`).
 *
 * Значение `weekly` удалено из API в swagger 1.2.1 и здесь не заводится.
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class BudgetFrequency extends AbstractEnum
{
    /** Дневной бюджет */
    public const DAILY = 'daily';

    /** Бюджет на весь период */
    public const ENTIRE_PERIOD = 'entirePeriod';
}
