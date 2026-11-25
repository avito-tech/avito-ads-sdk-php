<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип целевого действия, по которому рассчитаны CPA и CR (`Conversions::getGoalType()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class ConversionGoalType extends AbstractEnum
{
    /** Контакты */
    public const CONTACTS = 'contacts';

    /** Подписки */
    public const SUBSCRIPTIONS = 'subscriptions';

    /** Главная цель click-out кампании */
    public const CUSTOM_GOAL = 'customGoal';
}
