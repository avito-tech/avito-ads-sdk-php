<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Фильтр списка договоров.
 */
final class ContractsFilter extends AbstractFilter
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
     * @param string[] $numbers
     * @return self
     */
    public function numbers(array $numbers): self
    {
        return $this->set('numbers', array_values($numbers));
    }

    /**
     * Фильтр по исполнителям договора.
     * @return self
     */
    public function contractors(AdvertiserFilter $filter): self
    {
        return $this->set('contractors', $filter->toArray());
    }

    /**
     * Фильтр по заказчикам договора.
     * @return self
     */
    public function clients(AdvertiserFilter $filter): self
    {
        return $this->set('clients', $filter->toArray());
    }
}
