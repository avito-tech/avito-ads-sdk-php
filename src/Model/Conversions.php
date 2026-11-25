<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Конверсии кампании или группы (`StatsData::getConversions()`).
 *
 * null означает, что API не вернул значение; ноль — что значение известно и равно нулю.
 */
final class Conversions extends AbstractModel
{
    /** Контакты; только для click-in кампаний. */
    public function getContacts(): ?ConversionData
    {
        return $this->getModel('contacts', ConversionData::class);
    }

    /** Подписки; только для click-in кампаний. */
    public function getSubscriptions(): ?ConversionData
    {
        return $this->getModel('subscriptions', ConversionData::class);
    }

    /** Количество событий главной цели click-out кампании. */
    public function getGoalHits(): ?int
    {
        return $this->getInt('goalHits');
    }

    /** Стоимость целевого действия, в копейках. */
    public function getCpaKopeks(): ?int
    {
        return $this->getInt('cpaKopeks');
    }

    /**
     * Целевое действие, по которому рассчитаны CPA и CR: см. \AvitoAds\Enum\ConversionGoalType
     * (`contacts`, `subscriptions`, `customGoal`). Неизвестное значение возвращается как есть.
     */
    public function getGoalType(): ?string
    {
        return $this->getString('goalType');
    }

    /** Главная цель click-out кампании. */
    public function getCustomGoal(): ?CustomGoal
    {
        return $this->getModel('customGoal', CustomGoal::class);
    }

    /** CR: отношение целевых событий к кликам (0.05 = 5%). */
    public function getCr(): ?float
    {
        return $this->getFloat('cr');
    }
}
