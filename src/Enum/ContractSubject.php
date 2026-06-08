<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Предмет договора.
 */
final class ContractSubject extends AbstractEnum
{
    /** Распространение от организации */
    public const ORG_DISTRIBUTION = 'org-distribution';

    /** Посредничество */
    public const MEDIATION = 'mediation';

    /** Распространение */
    public const DISTRIBUTION = 'distribution';

    /** Представительство */
    public const REPRESENTATION = 'representation';

    /** Иное */
    public const OTHER = 'other';
}
