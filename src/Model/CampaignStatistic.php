<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Статистика кампании: как EntityStatistic, плюс модель оплаты и тип кампании.
 */
final class CampaignStatistic extends EntityStatistic
{
    /** Модель оплаты: см. \AvitoAds\Enum\CampaignPaymentModel. */
    public function getPaymentModel(): ?string
    {
        return $this->getString('paymentModel');
    }

    /** Тип кампании: см. \AvitoAds\Enum\CampaignType. */
    public function getCampaignType(): ?string
    {
        return $this->getString('campaignType');
    }
}
