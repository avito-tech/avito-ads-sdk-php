<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип договора.
 */
final class ContractType extends AbstractEnum
{
    /** Договор на оказание услуг */
    public const SERVICE = 'service';

    /** Посреднический договор */
    public const INTERMEDIARY = 'intermediary';

    /** Внешний договор (требует cid) */
    public const EXTERNAL = 'external';
}
