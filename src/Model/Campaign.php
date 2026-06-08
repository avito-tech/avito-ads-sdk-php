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

    /** Статус кампании: см. \AvitoAds\Enum\CampaignStatus. */
    public function getStatus(): ?string
    {
        return $this->getString('status');
    }

    /** Бюджет в рублях с НДС. */
    public function getBudget(): ?int
    {
        return $this->getInt('budget');
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

    public function getCreatedAt(): ?string
    {
        return $this->getString('createdAt');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getString('updatedAt');
    }
}
