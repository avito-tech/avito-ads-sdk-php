<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Набор показателей статистики за период (или за один день).
 *
 * Денежные значения (spend, spendBonus) — целые в рублях, spendKopeks и
 * spendBonusKopeks — точные значения в копейках; ставки и доли (ctr,
 * cpm, cpc, q*, vtr) — числа с плавающей точкой. Поля video* и q* заполняются
 * только для видеокампаний.
 */
final class StatsData extends AbstractModel
{
    /** Временная метка (по дням), ISO 8601. В totalData отсутствует. */
    public function getTimestamp(): ?string
    {
        return $this->getString('timestamp');
    }

    public function getViews(): int
    {
        return (int) $this->getInt('views');
    }

    public function getClicks(): int
    {
        return (int) $this->getInt('clicks');
    }

    /** CTR (Click-Through Rate). */
    public function getCtr(): float
    {
        return (float) $this->getFloat('ctr');
    }

    /** Потрачено денег, в рублях. */
    public function getSpend(): int
    {
        return (int) $this->getInt('spend');
    }

    /** Потрачено бонусов, в рублях. */
    public function getSpendBonus(): int
    {
        return (int) $this->getInt('spendBonus');
    }

    /** Потрачено денег, в копейках. */
    public function getSpendKopeks(): int
    {
        return (int) $this->getInt('spendKopeks');
    }

    /** Потрачено бонусов, в копейках. */
    public function getSpendBonusKopeks(): int
    {
        return (int) $this->getInt('spendBonusKopeks');
    }

    /** Средняя стоимость CPM. */
    public function getCpm(): float
    {
        return (float) $this->getFloat('cpm');
    }

    /** Средняя стоимость CPC. */
    public function getCpc(): float
    {
        return (float) $this->getFloat('cpc');
    }

    public function getVideoViews25(): ?int
    {
        return $this->getInt('videoViews25');
    }

    public function getVideoViews50(): ?int
    {
        return $this->getInt('videoViews50');
    }

    public function getVideoViews75(): ?int
    {
        return $this->getInt('videoViews75');
    }

    public function getVideoViews100(): ?int
    {
        return $this->getInt('videoViews100');
    }

    public function getQ25(): ?float
    {
        return $this->getFloat('q25');
    }

    public function getQ50(): ?float
    {
        return $this->getFloat('q50');
    }

    public function getQ75(): ?float
    {
        return $this->getFloat('q75');
    }

    /** View-Through Rate — доля просмотров на 100%. */
    public function getVtr(): ?float
    {
        return $this->getFloat('vtr');
    }
}
