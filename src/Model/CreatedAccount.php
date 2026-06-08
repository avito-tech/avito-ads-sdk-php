<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат создания тестового аккаунта в песочнице.
 */
final class CreatedAccount extends AbstractModel
{
    public function getAccountId(): int
    {
        return (int) $this->getInt('accountID');
    }
}
