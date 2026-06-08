<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат создания дочернего аккаунта.
 *
 * Возвращает ID нового аккаунта и пару ключей для выпуска его токена.
 * Внимание: в песочнице токен по этим ключам использовать нельзя.
 */
final class CreatedChildAccount extends AbstractModel
{
    public function getAccountId(): int
    {
        return (int) $this->getInt('accountID');
    }

    public function getClientKey(): ?string
    {
        return $this->getString('clientKey');
    }

    public function getClientSecret(): ?string
    {
        return $this->getString('clientSecret');
    }
}
