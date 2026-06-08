<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Фильтр списка рекламодателей.
 */
final class AdvertiserFilter extends AbstractFilter
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
     * @param string[] $inns
     * @return self
     */
    public function inns(array $inns): self
    {
        return $this->set('inns', array_values($inns));
    }

    /**
     * @param string[] $roles см. \AvitoAds\Enum\LegalRole
     * @return self
     */
    public function roles(array $roles): self
    {
        return $this->set('roles', array_values($roles));
    }
}
