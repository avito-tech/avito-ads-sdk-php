<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Фильтр списка кампаний.
 */
final class CampaignsFilter extends AbstractFilter
{
    /**
     * @param int[] $ids
     * @return self
     */
    public function ids(array $ids): self
    {
        return $this->set('ids', array_values($ids));
    }

    /**
     * @param string[] $models см. \AvitoAds\Enum\CampaignPaymentModel
     * @return self
     */
    public function paymentModels(array $models): self
    {
        return $this->set('paymentModels', array_values($models));
    }

    /**
     * @param string[] $types см. \AvitoAds\Enum\CampaignType
     * @return self
     */
    public function campaignTypes(array $types): self
    {
        return $this->set('campaignTypes', array_values($types));
    }

    /**
     * Значения не проверяются на клиенте: можно передавать любые статусы из
     * \AvitoAds\Enum\CampaignStatus, включая новые (`will_launch_soon`, `pausing` и др.).
     *
     * @param string[] $statuses см. \AvitoAds\Enum\CampaignStatus
     * @return self
     */
    public function statuses(array $statuses): self
    {
        return $this->set('statuses', array_values($statuses));
    }

    /**
     * @param int[] $managers
     * @return self
     */
    public function managers(array $managers): self
    {
        return $this->set('managers', array_values($managers));
    }

    /**
     * @param int[] $advertisers
     * @return self
     */
    public function advertisers(array $advertisers): self
    {
        return $this->set('advertisers', array_values($advertisers));
    }

    /**
     * @param int[] $contractIds
     * @return self
     */
    public function contractIds(array $contractIds): self
    {
        return $this->set('contractIDs', array_values($contractIds));
    }

    /**
     * @param int[] $ids
     * @return self
     */
    public function additionalAgreementIds(array $ids): self
    {
        return $this->set('additionalAgreementIDs', array_values($ids));
    }

    /**
     * Диапазон дат создания.
     * @return self
     */
    public function createdAt(DateRange $range): self
    {
        return $this->set('createdAt', $range->toArray());
    }

    /**
     * Диапазон периода размещения.
     * @return self
     */
    public function timeFrame(DateRange $range): self
    {
        return $this->set('timeFrame', $range->toArray());
    }
}
