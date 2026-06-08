<?php

declare(strict_types=1);

/**
 * Постраничная выборка и автоматический обход всех страниц.
 */

require __DIR__ . '/../vendor/autoload.php';

use AvitoAds\Client;
use AvitoAds\Configuration;

$client = new Client(
    Configuration::create(
        (string) getenv('AVITO_ADS_CLIENT_ID'),
        (string) getenv('AVITO_ADS_CLIENT_SECRET'),
        (int) getenv('AVITO_ADS_ACCOUNT_ID')
    )->sandbox()
);

// Вариант 1: постранично вручную.
$page = $client->campaigns()->list(null, 50, 1);
echo 'Всего кампаний: ', $page->getTotal(), PHP_EOL;
echo 'Остаток лимита API: ', (string) $page->getApiPointBalance(), PHP_EOL;
foreach ($page as $campaign) {
    echo '  - ', $campaign->getId(), ' ', $campaign->getName(), PHP_EOL;
}

// Вариант 2: обойти все страницы автоматически.
$count = 0;
foreach ($client->campaigns()->iterate() as $campaign) {
    $count++;
}
echo 'Обойдено кампаний через iterate(): ', $count, PHP_EOL;
