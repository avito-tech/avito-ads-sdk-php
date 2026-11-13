<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Управление ставкой группы (`Group::getPriceControl()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class GroupPriceControl extends AbstractEnum
{
    /** Ручное */
    public const MANUAL = 'manual';

    /** Автоматическое */
    public const AUTO = 'auto';
}
