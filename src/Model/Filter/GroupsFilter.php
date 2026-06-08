<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Фильтр списка групп объявлений.
 */
final class GroupsFilter extends AbstractFilter
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
     * @param int[] $campaignIds
     * @return self
     */
    public function campaignIds(array $campaignIds): self
    {
        return $this->set('campaignIDs', array_values($campaignIds));
    }

    /**
     * @param string[] $statuses см. \AvitoAds\Enum\GroupsStatus
     * @return self
     */
    public function statuses(array $statuses): self
    {
        return $this->set('statuses', array_values($statuses));
    }

    /**
     * @param string[] $paces скорости показа
     * @return self
     */
    public function paces(array $paces): self
    {
        return $this->set('paces', array_values($paces));
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
     * @param string[] $models см. \AvitoAds\Enum\CampaignPaymentModel
     * @return self
     */
    public function paymentModels(array $models): self
    {
        return $this->set('paymentModels', array_values($models));
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
