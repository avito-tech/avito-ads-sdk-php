<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Креатив — конкретное объявление внутри группы.
 */
final class Creative extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }

    public function getTitle(): ?string
    {
        return $this->getString('title');
    }

    public function getDescription(): ?string
    {
        return $this->getString('description');
    }

    public function getButtonText(): ?string
    {
        return $this->getString('buttonText');
    }

    public function getLink(): ?string
    {
        return $this->getString('link');
    }

    /** Статус креатива: см. \AvitoAds\Enum\CreativesStatus. */
    public function getStatus(): ?string
    {
        return $this->getString('status');
    }

    public function getGroupId(): ?int
    {
        return $this->getInt('groupID');
    }

    public function getCampaignId(): ?int
    {
        return $this->getInt('campaignID');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountID');
    }

    public function getAdvertiserId(): ?int
    {
        return $this->getInt('advertiserID');
    }

    public function getManagerId(): ?int
    {
        return $this->getInt('managerID');
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

    /**
     * Юридическая информация (LegalInfo) или [].
     *
     * @return array<string, mixed>
     */
    public function getLegalInfo(): array
    {
        return $this->getArray('legalInfo');
    }
}
