<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Темп открутки бюджета группы (`Group::getPace()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class Paces extends AbstractEnum
{
    /** Как можно быстрее */
    public const ASAP = 'asap';

    /** Равномерно */
    public const EVEN = 'even';
}
