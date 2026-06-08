<?php

declare(strict_types=1);

/**
 * Получение статистики по кампании за период (не более 100 дней).
 */

require __DIR__ . '/../vendor/autoload.php';

use AvitoAds\Client;
use AvitoAds\Configuration;
use AvitoAds\Exception\ValidationException;

$client = new Client(
    Configuration::create(
        (string) getenv('AVITO_ADS_CLIENT_ID'),
        (string) getenv('AVITO_ADS_CLIENT_SECRET'),
        (int) getenv('AVITO_ADS_ACCOUNT_ID')
    )->sandbox()
);

$campaignId = (int) (getenv('AVITO_ADS_CAMPAIGN_ID') ?: 0);

try {
    $stats = $client->statistics()->campaign($campaignId, '2025-01-01', '2025-01-31');
    $total = $stats->getCampaign()->getTotalData();
    echo 'Показы: ', $total->getViews(), PHP_EOL;
    echo 'Клики: ', $total->getClicks(), PHP_EOL;
    echo 'Расход: ', $total->getSpend(), ' ₽', PHP_EOL;
} catch (ValidationException $e) {
    fwrite(STDERR, 'Ошибка валидации: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
