<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат метода «статистика по кампании».
 *
 * Содержит статистику самой кампании, а также агрегаты по её группам и креативам.
 */
final class CampaignStatisticResult extends AbstractModel
{
    /** Статистика кампании. */
    public function getCampaign(): CampaignStatistic
    {
        return CampaignStatistic::fromArray($this->getArray('campaign'));
    }

    /**
     * Статистика по группам кампании.
     *
     * @return EntityStatistic[]
     */
    public function getGroups(): array
    {
        return array_map(
            static function ($row): EntityStatistic {
                return EntityStatistic::fromArray(is_array($row) ? $row : []);
            },
            $this->getArray('groups')
        );
    }

    /**
     * Статистика по креативам кампании.
     *
     * @return EntityStatistic[]
     */
    public function getCreatives(): array
    {
        return array_map(
            static function ($row): EntityStatistic {
                return EntityStatistic::fromArray(is_array($row) ? $row : []);
            },
            $this->getArray('creatives')
        );
    }
}
