<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Правило частотного ограничения показов группы: не более count показов за period.
 */
final class FrequencyRule extends AbstractModel
{
    /** Количество показов за период. */
    public function getCount(): ?int
    {
        return $this->getInt('count');
    }

    /** Период: см. \AvitoAds\Enum\FrequencyPeriod. */
    public function getPeriod(): ?string
    {
        return $this->getString('period');
    }
}
