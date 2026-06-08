<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Exception\ConfigurationException;
use AvitoAds\Model\Account;
use AvitoAds\Model\Balance;
use AvitoAds\Model\CreatedAccount;

/**
 * Методы работы с аккаунтом: реквизиты, баланс, создание тестового аккаунта.
 */
final class AccountResource extends AbstractResource
{
    /**
     * Реквизиты текущего аккаунта.
     *
     * GET /v1/account/{accountID}
     */
    public function get(): Account
    {
        $response = $this->http->request('GET', $this->accountPath());
        $data = $response->getData();
        $account = isset($data['account']) && is_array($data['account']) ? $data['account'] : $data;

        return Account::fromArray($account);
    }

    /**
     * Баланс текущего аккаунта (рубли и бонусы).
     *
     * GET /v1/account/{accountID}/balance
     */
    public function getBalance(): Balance
    {
        $response = $this->http->request('GET', $this->accountPath('balance'));

        return Balance::fromArray($response->getData());
    }

    /**
     * Создаёт тестовый аккаунт. Доступно только в песочнице.
     *
     * POST /v1/account/{accountID}
     *
     * @param array<string, mixed> $contact контактное лицо: ['name' => ..., 'phone' => ...]
     */
    public function createSandboxAccount(
        string $inn,
        string $shortName,
        string $longName,
        string $ogrn,
        string $legalAddress,
        string $actualAddress,
        array $contact,
        ?string $kpp = null,
        ?string $legalType = null
    ): CreatedAccount {
        if ($contact === []) {
            throw new ConfigurationException('Для создания аккаунта необходимо передать контактное лицо (contact).');
        }

        $body = [
            'inn' => $inn,
            'shortName' => $shortName,
            'longName' => $longName,
            'ogrn' => $ogrn,
            'legalAddress' => $legalAddress,
            'actualAddress' => $actualAddress,
            'contact' => $contact,
        ];
        if ($kpp !== null) {
            $body['kpp'] = $kpp;
        }
        if ($legalType !== null) {
            $body['legalType'] = $legalType;
        }

        $response = $this->http->request('POST', $this->accountPath(), $body);

        return CreatedAccount::fromArray($response->getData());
    }
}
