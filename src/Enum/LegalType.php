<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип юридического лица.
 */
final class LegalType extends AbstractEnum
{
    /** Юридическое лицо */
    public const UL = 'ul';

    /** Индивидуальный предприниматель */
    public const IP = 'ip';
}
