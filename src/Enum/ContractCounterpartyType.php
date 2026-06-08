<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Описание сторон договора (поле description).
 */
final class ContractCounterpartyType extends AbstractEnum
{
    /** Прямой договор с рекламодателем */
    public const DIRECT_WITH_ADVERTISER = 'direct_with_advertiser';

    /** Через посредника */
    public const ADVERTISER_INTERMEDIARY = 'advertiser_intermediary';
}
