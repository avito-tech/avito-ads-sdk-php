<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Описание действия договора (поле object).
 */
final class ContractAction extends AbstractEnum
{
    /** Распространение */
    public const DISTRIBUTION = 'distribution';

    /** Заключение */
    public const CONCLUDE = 'conclude';

    /** Коммерческое */
    public const COMMERCIAL = 'commercial';

    /** Иное */
    public const OTHER = 'other';
}
