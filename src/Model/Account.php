<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Реквизиты рекламного аккаунта (ЮЛ/ИП).
 */
final class Account extends AbstractModel
{
    /** ИНН организации/ИП. */
    public function getInn(): ?string
    {
        return $this->getString('inn');
    }

    /** Краткое название. */
    public function getShortName(): ?string
    {
        return $this->getString('shortName');
    }

    /** Полное наименование. */
    public function getLongName(): ?string
    {
        return $this->getString('longName');
    }

    /** ОГРН или ОГРНИП. */
    public function getOgrn(): ?string
    {
        return $this->getString('ogrn');
    }

    /** КПП организации (для ИП пусто). */
    public function getKpp(): ?string
    {
        return $this->getString('kpp');
    }

    /** Юридический адрес. */
    public function getLegalAddress(): ?string
    {
        return $this->getString('legalAddress');
    }

    /** Фактический адрес. */
    public function getActualAddress(): ?string
    {
        return $this->getString('actualAddress');
    }

    /**
     * Контактное лицо: ['name' => ..., 'phone' => ...].
     *
     * @return array<string, mixed>
     */
    public function getContact(): array
    {
        return $this->getArray('contact');
    }

    /**
     * Закреплённый менеджер MSD: ['name' => ..., 'email' => ...] или [].
     *
     * @return array<string, mixed>
     */
    public function getManager(): array
    {
        return $this->getArray('manager');
    }
}
