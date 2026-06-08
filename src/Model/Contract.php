<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Договор между аккаунтом и рекламодателем.
 */
final class Contract extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountID');
    }

    /** Заказчик договора. */
    public function getClient(): ?Advertiser
    {
        $client = $this->getArray('client');

        return $client === [] ? null : Advertiser::fromArray($client);
    }

    /** Исполнитель договора. */
    public function getContractor(): ?Advertiser
    {
        $contractor = $this->getArray('contractor');

        return $contractor === [] ? null : Advertiser::fromArray($contractor);
    }

    /** Тип договора: см. \AvitoAds\Enum\ContractType. */
    public function getType(): ?string
    {
        return $this->getString('type');
    }

    public function getDate(): ?string
    {
        return $this->getString('date');
    }

    public function getNumber(): ?string
    {
        return $this->getString('number');
    }

    public function getSubject(): ?string
    {
        return $this->getString('subject');
    }

    public function getObject(): ?string
    {
        return $this->getString('object');
    }

    /** Идентификатор внешнего договора (для type = external). */
    public function getCid(): ?string
    {
        return $this->getString('cid');
    }

    public function getDescription(): ?string
    {
        return $this->getString('description');
    }

    /** ID родительского договора (если это доп. соглашение). */
    public function getParentId(): ?int
    {
        return $this->getInt('parentId');
    }
}
