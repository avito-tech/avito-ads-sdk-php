<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Баланс аккаунта. Все значения — целые числа в рублях с НДС.
 */
final class Balance extends AbstractModel
{
    /** Денежные средства, в рублях. */
    public function getBalance(): int
    {
        return (int) $this->getInt('balance');
    }

    /** Бонусы, в рублях. */
    public function getBonusBalance(): int
    {
        return (int) $this->getInt('bonusBalance');
    }
}
