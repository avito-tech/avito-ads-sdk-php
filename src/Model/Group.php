<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Группа объявлений внутри кампании (бюджет, ставка, расписание, таргетинг).
 *
 * Значения перечислений (статус, формат, период бюджета и т. п.) возвращаются
 * строками как есть, неизвестные значения не вызывают ошибку. Сырые данные
 * доступны через get() и toArray().
 */
final class Group extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountID');
    }

    public function getCampaignId(): ?int
    {
        return $this->getInt('campaignID');
    }

    /**
     * Статус группы: см. \AvitoAds\Enum\GroupsStatus.
     *
     * Неизвестный статус возвращается строкой как есть.
     */
    public function getStatus(): ?string
    {
        return $this->getString('status');
    }

    /**
     * Все коды причин текущего статуса; [] — причин нет.
     *
     * Порядок не отражает приоритет. API может добавлять новые коды: неизвестные
     * значения возвращаются как есть.
     *
     * @return string[]
     */
    public function getStatusReasons(): array
    {
        return $this->getStringList('statusReasons');
    }

    /**
     * Главная причина текущего статуса; "" — главная причина не определена
     * (даже если getStatusReasons() не пуст).
     */
    public function getTopStatusReason(): string
    {
        return (string) $this->getString('topStatusReason');
    }

    /**
     * Доступные действия с группой.
     *
     * @return string[]
     */
    public function getActions(): array
    {
        return $this->getStringList('actions');
    }

    /** Бюджет в рублях с НДС. */
    public function getBudget(): ?int
    {
        return $this->getInt('budget');
    }

    /** Бюджет в копейках с НДС. */
    public function getBudgetKopeks(): ?int
    {
        return $this->getInt('budgetKopeks');
    }

    /**
     * Период бюджета группы: см. \AvitoAds\Enum\BudgetFrequency (`daily`, `entirePeriod`).
     * Неизвестное значение возвращается как есть.
     */
    public function getBudgetFrequency(): ?string
    {
        return $this->getString('budgetFrequency');
    }

    /** Цена (ставка) в рублях с НДС. */
    public function getPrice(): ?int
    {
        return $this->getInt('price');
    }

    /** Цена (ставка) в копейках с НДС. */
    public function getPriceKopeks(): ?int
    {
        return $this->getInt('priceKopeks');
    }

    /** Управление ставкой: см. \AvitoAds\Enum\GroupPriceControl. */
    public function getPriceControl(): ?string
    {
        return $this->getString('priceControl');
    }

    /** Формат группы: см. \AvitoAds\Enum\GroupFormat. */
    public function getFormat(): ?string
    {
        return $this->getString('format');
    }

    /** Тип аудитории таргетинга: см. \AvitoAds\Enum\AudienceType. */
    public function getAudienceType(): ?string
    {
        return $this->getString('audienceType');
    }

    /** Темп показов: см. \AvitoAds\Enum\Paces. */
    public function getPace(): ?string
    {
        return $this->getString('pace');
    }

    /**
     * Правила частотного ограничения показов.
     *
     * @return FrequencyRule[]
     */
    public function getFrequencyRules(): array
    {
        return $this->getModelList('frequencyRules', FrequencyRule::class);
    }

    /**
     * Частота показов в рамках периода (первое правило частотного ограничения).
     *
     * @deprecated Сохраняется в ответе для совместимости и соответствует первому
     *             правилу. Используйте getFrequencyRules().
     */
    public function getFrequencyCount(): ?int
    {
        return $this->getInt('frequencyCount');
    }

    /**
     * Период частотного ограничения (первое правило): см. \AvitoAds\Enum\FrequencyPeriod.
     *
     * @deprecated Сохраняется в ответе для совместимости и соответствует первому
     *             правилу. Используйте getFrequencyRules().
     */
    public function getFrequencyPeriod(): ?string
    {
        return $this->getString('frequencyPeriod');
    }

    /** Тип расписания показов: см. \AvitoAds\Enum\ScheduleType. */
    public function getScheduleType(): ?string
    {
        return $this->getString('scheduleType');
    }

    /**
     * Расписание активности по дням: день недели (`monday` … `sunday`) → часы 0–23.
     *
     * @return array<string, int[]>
     */
    public function getActivitySchedule(): array
    {
        $result = [];
        foreach ($this->getArray('activitySchedule') as $day => $hours) {
            if (!is_array($hours)) {
                continue;
            }
            $result[(string) $day] = array_values(array_map('intval', array_filter($hours, 'is_numeric')));
        }

        return $result;
    }

    /** Запускать как можно скорее. */
    public function getStartAsap(): ?bool
    {
        return $this->getBool('startASAP');
    }

    /** Начало периода показов, ISO 8601. */
    public function getPeriodStartAt(): ?string
    {
        return $this->getString('periodStartAt');
    }

    /** Конец периода показов, ISO 8601. */
    public function getPeriodEndAt(): ?string
    {
        return $this->getString('periodEndAt');
    }

    /** Таймзона периода показов. */
    public function getPeriodTimezone(): ?string
    {
        return $this->getString('periodTimezone');
    }

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

    public function getAdvertiserId(): ?int
    {
        return $this->getInt('advertiserID');
    }

    public function getManagerId(): ?int
    {
        return $this->getInt('managerID');
    }

    /** ID пользователя, создавшего группу. */
    public function getUserId(): ?int
    {
        return $this->getInt('userID');
    }

    public function getHaveCreative(): ?bool
    {
        return $this->getBool('haveCreative');
    }

    public function getCreatedAt(): ?string
    {
        return $this->getString('createdAt');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getString('updatedAt');
    }

    /**
     * ID возрастных сегментов.
     *
     * @return int[]
     */
    public function getTargetingAgeIds(): array
    {
        return $this->getIntList('targetingAgeIDs');
    }

    /**
     * ID аудиторий.
     *
     * @return string[]
     */
    public function getTargetingAudienceIds(): array
    {
        return $this->getStringList('targetingAudienceIDs');
    }

    /**
     * ID семейного статуса.
     *
     * @return int[]
     */
    public function getTargetingFamilyStatusIds(): array
    {
        return $this->getIntList('targetingFamilyStatusIDs');
    }

    /**
     * ID пола.
     *
     * @return int[]
     */
    public function getTargetingGenderIds(): array
    {
        return $this->getIntList('targetingGenderIDs');
    }

    /**
     * ID доходных сегментов.
     *
     * @return int[]
     */
    public function getTargetingIncomeIds(): array
    {
        return $this->getIntList('targetingIncomeIDs');
    }

    /**
     * ID интересов.
     *
     * @return string[]
     */
    public function getTargetingInterestIds(): array
    {
        return $this->getStringList('targetingInterestIDs');
    }

    /**
     * ID локаций.
     *
     * @return int[]
     */
    public function getTargetingLocationIds(): array
    {
        return $this->getIntList('targetingLocationIDs');
    }

    /**
     * ID родительского статуса.
     *
     * @return int[]
     */
    public function getTargetingParentStatusIds(): array
    {
        return $this->getIntList('targetingParentStatusIDs');
    }
}
