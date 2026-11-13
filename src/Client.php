<?php

declare(strict_types=1);

namespace AvitoAds;

use AvitoAds\Auth\TokenProviderInterface;
use AvitoAds\Http\HttpClientInterface;
use AvitoAds\Resource\AccountResource;
use AvitoAds\Resource\AdvertiserResource;
use AvitoAds\Resource\CampaignResource;
use AvitoAds\Resource\ChildAccountResource;
use AvitoAds\Resource\ContractResource;
use AvitoAds\Resource\CreativeResource;
use AvitoAds\Resource\GroupResource;
use AvitoAds\Resource\StatisticsResource;
use AvitoAds\Resource\UserResource;

/**
 * Главная точка входа в клиент Авито Реклама.
 *
 * Создаётся из объекта Configuration и предоставляет доступ к группам методов
 * (ресурсам). Все запросы выполняются от лица аккаунта, заданного в конфигурации
 * (токен привязан к одному аккаунту).
 *
 * Пример:
 *
 *     $config = \AvitoAds\Configuration::create('CLIENT_ID', 'CLIENT_SECRET', 123456789)
 *         ->production();
 *     $client = new \AvitoAds\Client($config);
 *     $balance = $client->account()->getBalance();
 *
 * Версия SDK совпадает с версией публичного API (swagger), которую он реализует.
 */
final class Client
{
    /**
     * Версия SDK. Единственный источник версии в коде: из неё собирается User-Agent
     * (`avito-ads-sdk-php/<версия>`). Packagist берёт версию пакета из git-тега `v<версия>`.
     */
    public const VERSION = '1.4.0';

    /**
     * Версия публичного API Авито Рекламы (`info.version` в `openapi.json`), которую реализует SDK.
     */
    public const API_VERSION = '1.4.0';

    /** @var Configuration */
    private $config;

    /** @var HttpClientInterface */
    private $http;

    /** @var TokenProviderInterface */
    private $tokenProvider;

    /** @var array<string, mixed> Кэш созданных ресурсов. */
    private $resources = [];

    /**
     * @param HttpClientInterface|null    $http          подмена транспорта (для тестов)
     * @param TokenProviderInterface|null $tokenProvider подмена поставщика токена (для тестов)
     */
    public function __construct(
        Configuration $config,
        ?HttpClientInterface $http = null,
        ?TokenProviderInterface $tokenProvider = null
    ) {
        $this->config = $config;
        $this->tokenProvider = $tokenProvider ?? ClientBuilder::buildTokenProvider($config);
        $this->http = $http ?? ClientBuilder::buildHttpClient($config, $this->tokenProvider);
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->http;
    }

    public function getTokenProvider(): TokenProviderInterface
    {
        return $this->tokenProvider;
    }

    /**
     * Реквизиты, баланс, создание тестового аккаунта.
     */
    public function account(): AccountResource
    {
        return $this->resource(AccountResource::class);
    }

    /**
     * Дочерние аккаунты и переводы средств/бонусов.
     */
    public function childAccounts(): ChildAccountResource
    {
        return $this->resource(ChildAccountResource::class);
    }

    /**
     * Рекламодатели.
     */
    public function advertisers(): AdvertiserResource
    {
        return $this->resource(AdvertiserResource::class);
    }

    /**
     * Договоры.
     */
    public function contracts(): ContractResource
    {
        return $this->resource(ContractResource::class);
    }

    /**
     * Кампании.
     */
    public function campaigns(): CampaignResource
    {
        return $this->resource(CampaignResource::class);
    }

    /**
     * Группы объявлений.
     */
    public function groups(): GroupResource
    {
        return $this->resource(GroupResource::class);
    }

    /**
     * Креативы.
     */
    public function creatives(): CreativeResource
    {
        return $this->resource(CreativeResource::class);
    }

    /**
     * Статистика по кампаниям, группам и креативам.
     */
    public function statistics(): StatisticsResource
    {
        return $this->resource(StatisticsResource::class);
    }

    /**
     * Пользователи аккаунта.
     */
    public function users(): UserResource
    {
        return $this->resource(UserResource::class);
    }

    /**
     * @template T of \AvitoAds\Resource\AbstractResource
     * @param class-string<T> $class
     * @return T
     */
    private function resource(string $class)
    {
        if (!isset($this->resources[$class])) {
            $this->resources[$class] = new $class($this->http, $this->config->getAccountId());
        }

        /** @var T $resource */
        $resource = $this->resources[$class];

        return $resource;
    }
}
