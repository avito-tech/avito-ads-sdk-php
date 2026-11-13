<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип аудитории группы (`Group::getAudienceType()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class AudienceType extends AbstractEnum
{
    public const BASIC = 'basic';
    public const READY = 'ready';
    public const AUTOTARGETING = 'autotargeting';
}
