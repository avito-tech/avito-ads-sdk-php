<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Рекламодатель — ЮЛ/ИП, от лица которого размещается реклама.
 */
final class Advertiser extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getShortName(): ?string
    {
        return $this->getString('shortName');
    }

    public function getLongName(): ?string
    {
        return $this->getString('longName');
    }

    public function getInn(): ?string
    {
        return $this->getString('inn');
    }

    public function getOgrn(): ?string
    {
        return $this->getString('ogrn');
    }

    public function getKpp(): ?string
    {
        return $this->getString('kpp');
    }

    public function getLegalAddress(): ?string
    {
        return $this->getString('legalAddress');
    }

    public function getActualAddress(): ?string
    {
        return $this->getString('actualAddress');
    }

    /** Тип ЮЛ: см. \AvitoAds\Enum\LegalType. */
    public function getLegalType(): ?string
    {
        return $this->getString('legalType');
    }

    /** Роль ЮЛ: см. \AvitoAds\Enum\LegalRole. */
    public function getLegalRole(): ?string
    {
        return $this->getString('legalRole');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountId');
    }
}
