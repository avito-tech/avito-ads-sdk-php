<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Группа объявлений внутри кампании (бюджет, ставка, расписание, таргетинг).
 *
 * Из-за большого числа полей здесь вынесены типизированные геттеры для самых
 * востребованных; остальные доступны через метод get() и toArray().
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

    /** Статус группы: см. \AvitoAds\Enum\GroupsStatus. */
    public function getStatus(): ?string
    {
        return $this->getString('status');
    }

    /** Бюджет в рублях с НДС. */
    public function getBudget(): ?int
    {
        return $this->getInt('budget');
    }

    /** Цена (ставка) в рублях с НДС. */
    public function getPrice(): ?int
    {
        return $this->getInt('price');
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
}
