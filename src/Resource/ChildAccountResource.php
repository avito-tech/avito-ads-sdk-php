<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\ChildAccount;
use AvitoAds\Model\CreatedChildAccount;

/**
 * Методы работы с дочерними аккаунтами и переводами средств.
 */
final class ChildAccountResource extends AbstractResource
{
    /**
     * Список дочерних аккаунтов (с договорами).
     *
     * GET /v1/account/{accountID}/children
     *
     * @return ChildAccount[]
     */
    public function list(): array
    {
        $response = $this->http->request('GET', $this->accountPath('children'));
        $children = $response->getData()['children'] ?? [];

        return array_map(
            static function ($row): ChildAccount {
                return ChildAccount::fromArray(is_array($row) ? $row : []);
            },
            is_array($children) ? array_values($children) : []
        );
    }

    /**
     * Список дочерних аккаунтов с балансами.
     *
     * GET /v1/account/{accountID}/children-with-balances
     *
     * @return ChildAccount[]
     */
    public function listWithBalances(): array
    {
        $response = $this->http->request('GET', $this->accountPath('children-with-balances'));
        $children = $response->getData()['children'] ?? [];

        return array_map(
            static function ($row): ChildAccount {
                return ChildAccount::fromArray(is_array($row) ? $row : []);
            },
            is_array($children) ? array_values($children) : []
        );
    }

    /**
     * Создаёт дочерний аккаунт-непокупатель.
     *
     * POST /v1/account/{accountID}/create-nonpayer-child-account
     */
    public function createNonpayerChild(string $shortName, bool $isSelfAdvertisingEnabled): CreatedChildAccount
    {
        $response = $this->http->request('POST', $this->accountPath('create-nonpayer-child-account'), [
            'shortName' => $shortName,
            'isSelfAdvertisingEnabled' => $isSelfAdvertisingEnabled,
        ]);

        return CreatedChildAccount::fromArray($response->getData());
    }

    /**
     * Перевод денежных средств на другой аккаунт.
     *
     * POST /v1/account/{accountID}/funds-transfer
     *
     * @param int $accountIdTo аккаунт-получатель
     * @param int $amount      сумма в рублях, не менее 1
     */
    public function transferFunds(int $accountIdTo, int $amount): void
    {
        $this->assertPositiveAmount($amount);
        $this->http->request('POST', $this->accountPath('funds-transfer'), [
            'accountIdTo' => $accountIdTo,
            'amount' => $amount,
        ]);
    }

    /**
     * Перевод бонусов на другой аккаунт.
     *
     * POST /v1/account/{accountID}/bonus-transfer
     *
     * @param int $accountIdTo аккаунт-получатель
     * @param int $amount      сумма бонусов, не менее 1
     */
    public function transferBonus(int $accountIdTo, int $amount): void
    {
        $this->assertPositiveAmount($amount);
        $this->http->request('POST', $this->accountPath('bonus-transfer'), [
            'accountIdTo' => $accountIdTo,
            'amount' => $amount,
        ]);
    }

    private function assertPositiveAmount(int $amount): void
    {
        if ($amount < 1) {
            throw new ValidationException(sprintf('Сумма перевода должна быть не менее 1, передано %d.', $amount));
        }
    }
}
