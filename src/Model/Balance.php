<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Баланс аккаунта. Все значения — целые числа с НДС: в рублях и точно в копейках.
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

    /** Денежные средства, в копейках. */
    public function getBalanceKopeks(): int
    {
        return (int) $this->getInt('balanceKopeks');
    }

    /** Бонусы, в копейках. */
    public function getBonusBalanceKopeks(): int
    {
        return (int) $this->getInt('bonusBalanceKopeks');
    }
}
