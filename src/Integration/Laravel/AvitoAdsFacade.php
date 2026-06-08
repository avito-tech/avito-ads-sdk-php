<?php

declare(strict_types=1);

namespace AvitoAds\Integration\Laravel;

use AvitoAds\Client;
use Illuminate\Support\Facades\Facade;

/**
 * Фасад Laravel для доступа к клиенту Авито Реклама.
 *
 * Пример:
 *
 *     use AvitoAds\Integration\Laravel\AvitoAdsFacade as AvitoAds;
 *
 *     $balance = AvitoAds::account()->getBalance();
 *
 * @method static \AvitoAds\Resource\AccountResource account()
 * @method static \AvitoAds\Resource\ChildAccountResource childAccounts()
 * @method static \AvitoAds\Resource\AdvertiserResource advertisers()
 * @method static \AvitoAds\Resource\ContractResource contracts()
 * @method static \AvitoAds\Resource\CampaignResource campaigns()
 * @method static \AvitoAds\Resource\GroupResource groups()
 * @method static \AvitoAds\Resource\CreativeResource creatives()
 * @method static \AvitoAds\Resource\StatisticsResource statistics()
 * @method static \AvitoAds\Resource\UserResource users()
 *
 * @see \AvitoAds\Client
 */
final class AvitoAdsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
