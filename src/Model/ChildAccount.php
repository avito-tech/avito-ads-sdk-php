<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Дочерний аккаунт.
 *
 * В зависимости от метода может содержать договор (children) либо баланс
 * (children-with-balances). Удобные геттеры покрывают оба случая.
 */
final class ChildAccount extends AbstractModel
{
    public function getId(): ?int
    {
        $account = $this->getArray('account');

        return isset($account['id']) ? (int) $account['id'] : null;
    }

    public function getShortName(): ?string
    {
        $account = $this->getArray('account');

        return isset($account['shortName']) ? (string) $account['shortName'] : null;
    }

    /**
     * Договор дочернего аккаунта (для метода children), либо null.
     *
     * @return array<string, mixed>|null
     */
    public function getContract(): ?array
    {
        $contract = $this->getArray('contract');

        return $contract === [] ? null : $contract;
    }

    /**
     * Баланс дочернего аккаунта (для метода children-with-balances), либо null.
     */
    public function getBalance(): ?Balance
    {
        $balance = $this->getArray('balance');

        return $balance === [] ? null : Balance::fromArray($balance);
    }
}
