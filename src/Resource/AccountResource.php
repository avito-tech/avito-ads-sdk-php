<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Enum\LegalType;
use AvitoAds\Exception\ConfigurationException;
use AvitoAds\Exception\ValidationException;
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
     * Вместе с аккаунтом API готовит тестовые кампании, группы и креативы. Если
     * часть данных не подготовилась, API отвечает `207 Multi-Status`: аккаунт создан,
     * причины — в CreatedAccount::getWarnings().
     *
     * Порядок параметров соответствует swagger 1.4.0: сначала обязательные поля
     * (`legalType` обязателен), затем необязательные `actualAddress` и `kpp`.
     *
     * @param string               $legalType     см. \AvitoAds\Enum\LegalType
     * @param array<string, mixed> $contact       контактное лицо: ['name' => ..., 'phone' => ...]
     * @param string|null          $actualAddress фактический адрес; если не передан, API берёт legalAddress
     * @param string|null          $kpp           КПП организации
     *
     * @throws ValidationException если legalType не из LegalType
     */
    public function createSandboxAccount(
        string $inn,
        string $shortName,
        string $longName,
        string $ogrn,
        string $legalAddress,
        string $legalType,
        array $contact,
        ?string $actualAddress = null,
        ?string $kpp = null
    ): CreatedAccount {
        LegalType::assertValid($legalType);
        if ($contact === []) {
            throw new ConfigurationException('Для создания аккаунта необходимо передать контактное лицо (contact).');
        }

        $body = [
            'inn' => $inn,
            'shortName' => $shortName,
            'longName' => $longName,
            'ogrn' => $ogrn,
            'legalAddress' => $legalAddress,
            'legalType' => $legalType,
            'contact' => $contact,
        ];
        if ($actualAddress !== null) {
            $body['actualAddress'] = $actualAddress;
        }
        if ($kpp !== null) {
            $body['kpp'] = $kpp;
        }

        $response = $this->http->request('POST', $this->accountPath(), $body);

        return CreatedAccount::fromArray($response->getData());
    }
}
