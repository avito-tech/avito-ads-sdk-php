<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Роль юридического лица.
 */
final class LegalRole extends AbstractEnum
{
    /** Рекламодатель */
    public const ADVERTISER = 'rd';

    /** Рекламное агентство */
    public const AGENCY = 'ra';

    /** Рекламораспространитель */
    public const DISTRIBUTOR = 'rr';
}
