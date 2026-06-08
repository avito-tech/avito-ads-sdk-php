<?php

declare(strict_types=1);

namespace AvitoAds\Integration\CodeIgniter;

use AvitoAds\Client;
use AvitoAds\Configuration;
use CodeIgniter\Config\BaseService;

/**
 * Сервис-фабрика клиента Авито Реклама для CodeIgniter 4.
 *
 * Подключение: добавьте в app/Config/Services.php метод-обёртку:
 *
 *     public static function avitoAds(bool $getShared = true): \AvitoAds\Client
 *     {
 *         return \AvitoAds\Integration\CodeIgniter\Services::avitoAds($getShared);
 *     }
 *
 * После этого клиент доступен так:
 *
 *     $client = service('avitoAds');
 *     $balance = $client->account()->getBalance();
 */
class Services extends BaseService
{
    public static function avitoAds(bool $getShared = true): Client
    {
        if ($getShared) {
            /** @var Client $shared */
            $shared = static::getSharedInstance('avitoAds');

            return $shared;
        }

        /** @var AvitoAds $settings */
        $settings = config('AvitoAds');

        $configuration = Configuration::create(
            $settings->clientId,
            $settings->clientSecret,
            $settings->accountId
        );

        $configuration = $settings->environment === 'sandbox'
            ? $configuration->sandbox()
            : $configuration->production();

        $configuration = $configuration
            ->withTimeout($settings->timeout)
            ->withConnectTimeout($settings->connectTimeout)
            ->withMaxRetries($settings->maxRetries)
            ->withRetryBaseDelay($settings->retryBaseDelayMs)
            ->withTokenLeeway($settings->tokenLeeway);

        return new Client($configuration);
    }
}
