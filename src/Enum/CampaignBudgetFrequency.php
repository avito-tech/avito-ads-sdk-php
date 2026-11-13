<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Период бюджета кампании (`Campaign::getBudgetFrequency()`).
 *
 * `mixed` — в кампании есть группы и с дневным бюджетом, и с бюджетом на весь период;
 * у такой кампании `budget` и `budgetKopeks` отсутствуют (null).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class CampaignBudgetFrequency extends AbstractEnum
{
    /** Дневной бюджет у всех групп */
    public const DAILY = 'daily';

    /** Бюджет на весь период у всех групп */
    public const ENTIRE_PERIOD = 'entirePeriod';

    /** Группы с разными периодами бюджета */
    public const MIXED = 'mixed';
}
