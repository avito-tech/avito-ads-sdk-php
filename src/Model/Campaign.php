<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Рекламная кампания.
 */
final class Campaign extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }

    /**
     * Статус кампании: см. \AvitoAds\Enum\CampaignStatus.
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
     * Сумма бюджетов групп в целых рублях с НДС.
     *
     * null — суммы нет: у кампании со смешанным периодом бюджета
     * (getBudgetFrequency() === CampaignBudgetFrequency::MIXED) поле отсутствует.
     * 0 — пустая кампания. Не приводите null к 0: это разные состояния.
     */
    public function getBudget(): ?int
    {
        return $this->getInt('budget');
    }

    /**
     * Точная сумма бюджетов групп в копейках с НДС; null — как у getBudget().
     */
    public function getBudgetKopeks(): ?int
    {
        return $this->getInt('budgetKopeks');
    }

    /**
     * Период бюджета кампании: см. \AvitoAds\Enum\CampaignBudgetFrequency
     * (`daily`, `entirePeriod`, `mixed`). Неизвестное значение возвращается как есть.
     */
    public function getBudgetFrequency(): ?string
    {
        return $this->getString('budgetFrequency');
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

    public function getStartDate(): ?string
    {
        return $this->getString('startDate');
    }

    public function getEndDate(): ?string
    {
        return $this->getString('endDate');
    }

    public function getUserId(): ?int
    {
        return $this->getInt('userId');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountId');
    }

    public function getAdvertiserId(): ?int
    {
        return $this->getInt('advertiserId');
    }

    public function getContractId(): ?int
    {
        return $this->getInt('contractId');
    }

    public function getManagerId(): ?int
    {
        return $this->getInt('managerID');
    }

    public function getAdditionalAgreementId(): ?int
    {
        return $this->getInt('additionalAgreementID');
    }

    /** ID доп. соглашения к исполнительскому договору. */
    public function getPerformanceAdditionalAgreementId(): ?int
    {
        return $this->getInt('performanceAdditionalAgreementID');
    }

    public function getCreatedAt(): ?string
    {
        return $this->getString('createdAt');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getString('updatedAt');
    }
}
