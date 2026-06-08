<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Модель оплаты кампании.
 */
final class CampaignPaymentModel extends AbstractEnum
{
    /** За показы */
    public const CPM = 'CPM';

    /** За клики */
    public const CPC = 'CPC';
}
