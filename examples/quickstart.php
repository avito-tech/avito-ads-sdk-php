<?php

declare(strict_types=1);

/**
 * Быстрый старт: получение баланса и реквизитов аккаунта.
 *
 * Запуск:
 *   AVITO_ADS_CLIENT_ID=... AVITO_ADS_CLIENT_SECRET=... AVITO_ADS_ACCOUNT_ID=... \
 *   php examples/quickstart.php
 */

require __DIR__ . '/../vendor/autoload.php';

use AvitoAds\Client;
use AvitoAds\Configuration;
use AvitoAds\Exception\ApiException;

$config = Configuration::create(
    (string) getenv('AVITO_ADS_CLIENT_ID'),
    (string) getenv('AVITO_ADS_CLIENT_SECRET'),
    (int) getenv('AVITO_ADS_ACCOUNT_ID')
)->sandbox(); // для боевого окружения замените на ->production()

$client = new Client($config);

try {
    $account = $client->account()->get();
    echo 'Аккаунт: ', $account->getShortName(), ' (ИНН ', $account->getInn(), ')', PHP_EOL;

    $balance = $client->account()->getBalance();
    echo 'Баланс: ', $balance->getBalance(), ' ₽', PHP_EOL;
    echo 'Бонусы: ', $balance->getBonusBalance(), PHP_EOL;
} catch (ApiException $e) {
    fwrite(STDERR, sprintf("Ошибка API %d: %s%s", $e->getStatusCode(), $e->getMessage(), PHP_EOL));
    exit(1);
}
