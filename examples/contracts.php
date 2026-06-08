<?php

declare(strict_types=1);

/**
 * Создание посреднического договора через ContractBuilder и вывод списка договоров.
 */

require __DIR__ . '/../vendor/autoload.php';

use AvitoAds\Client;
use AvitoAds\Configuration;
use AvitoAds\Enum\ContractCounterpartyType;
use AvitoAds\Exception\ValidationException;
use AvitoAds\Resource\ContractBuilder;

$client = new Client(
    Configuration::create(
        (string) getenv('AVITO_ADS_CLIENT_ID'),
        (string) getenv('AVITO_ADS_CLIENT_SECRET'),
        (int) getenv('AVITO_ADS_ACCOUNT_ID')
    )->sandbox()
);

try {
    $builder = ContractBuilder::intermediaryContract()
        ->advertiser((int) (getenv('AVITO_ADS_ADVERTISER_ID') ?: 0))
        ->counterpartyType(ContractCounterpartyType::DIRECT_WITH_ADVERTISER)
        ->subject('mediation')
        ->object('commercial')
        ->reportingRequired(true)
        ->fundsAllocationToPrincipal(false)
        ->date('2025-01-15')
        ->number('ДА-2025/01')
        ->intermediary([
            'shortName' => 'ООО Реклама',
            'longName' => 'Общество с ограниченной ответственностью «Реклама»',
            'inn' => '7712345678',
            'ogrn' => '1177746123456',
            'kpp' => '771701001',
            'legalAddress' => 'г. Москва, ул. Примерная, д. 1',
            'actualAddress' => 'г. Москва, ул. Примерная, д. 1',
            'legalType' => 'ul',
        ]);

    $created = $client->contracts()->create($builder);
    echo 'Создан договор ID: ', $created->getId(), PHP_EOL;
} catch (ValidationException $e) {
    fwrite(STDERR, 'Договор не прошёл валидацию: ' . $e->getMessage() . PHP_EOL);
}

foreach ($client->contracts()->iterate() as $contract) {
    echo $contract->getId(), ' ', (string) $contract->getNumber(), ' [', (string) $contract->getType(), "]\n";
}
