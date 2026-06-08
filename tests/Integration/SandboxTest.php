<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Integration;

use AvitoAds\Client;
use AvitoAds\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционные тесты против реальной песочницы Авито Реклама.
 *
 * По умолчанию пропускаются. Чтобы запустить, задайте переменные окружения:
 *   AVITO_ADS_CLIENT_ID, AVITO_ADS_CLIENT_SECRET, AVITO_ADS_ACCOUNT_ID
 * и выполните:
 *   AVITO_ADS_RUN_INTEGRATION=1 vendor/bin/phpunit --testsuite integration
 *
 * Тесты используют только безопасные операции чтения.
 */
final class SandboxTest extends TestCase
{
    private function client(): Client
    {
        $clientId = getenv('AVITO_ADS_CLIENT_ID');
        $clientSecret = getenv('AVITO_ADS_CLIENT_SECRET');
        $accountId = getenv('AVITO_ADS_ACCOUNT_ID');

        if (
            getenv('AVITO_ADS_RUN_INTEGRATION') !== '1'
            || $clientId === false
            || $clientSecret === false
            || $accountId === false
        ) {
            self::markTestSkipped('Интеграционные тесты отключены (нет AVITO_ADS_RUN_INTEGRATION или учётных данных).');
        }

        $config = Configuration::create((string) $clientId, (string) $clientSecret, (int) $accountId)
            ->sandbox();

        return new Client($config);
    }

    public function testCanReadBalance(): void
    {
        $balance = $this->client()->account()->getBalance();
        self::assertIsInt($balance->getBalance());
        self::assertIsInt($balance->getBonusBalance());
    }

    public function testCanListCampaigns(): void
    {
        $result = $this->client()->campaigns()->list();
        self::assertGreaterThanOrEqual(0, $result->getTotal());
    }
}
