<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Статистика сущности (кампании, группы или креатива).
 *
 * Содержит идентификацию сущности, дневной разрез data[] и агрегат totalData.
 */
final class EntityStatistic extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }

    /** ID группы (только для статистики креатива). */
    public function getGroupId(): ?int
    {
        return $this->getInt('groupId');
    }

    /** Модель оплаты (только для статистики кампании). */
    public function getPaymentModel(): ?string
    {
        return $this->getString('paymentModel');
    }

    /** Тип кампании (только для статистики кампании). */
    public function getCampaignType(): ?string
    {
        return $this->getString('campaignType');
    }

    /**
     * Статистика по дням.
     *
     * @return StatsData[]
     */
    public function getData(): array
    {
        return array_map(
            static function ($row): StatsData {
                return StatsData::fromArray(is_array($row) ? $row : []);
            },
            $this->getArray('data')
        );
    }

    /**
     * Агрегированная статистика за весь период.
     */
    public function getTotalData(): StatsData
    {
        return StatsData::fromArray($this->getArray('totalData'));
    }
}
