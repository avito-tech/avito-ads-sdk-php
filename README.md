# Avito Ads PHP Client

- [Об Авито Реклама](http://avito.ru/ads)
- [Возможности API Авито Реклама](https://ads-help.avito.com/external/api)
- [Техническая документация API](https://developers.avito.ru/api-catalog/ads/documentation)

PHP-клиент для API **Авито Реклама** (Avito Ads API).

Библиотека закрывает все основные методы API: аккаунт и баланс, дочерние аккаунты и переводы средств, рекламодателей, договоры, кампании, группы объявлений, креативы, статистику и управление пользователями. Транспорт построен на [Guzzle](https://docs.guzzlephp.org/), авторизация — OAuth2 `client_credentials` с автоматическим обновлением и кэшированием токена, а также повторными попытками при ошибках `429` и `5xx`.

## Возможности

- Поддержка **PHP 7.4+ и PHP 8.x**.
- OAuth2 `client_credentials`: автоматическое получение, кэширование и обновление токена.
- Раздельные окружения: **production** и **sandbox** (песочница).
- Автоматические повторы при `429`/`5xx` с экспоненциальной задержкой и учётом заголовка `Retry-After`; однократное обновление токена и повтор при `401`.
- Типизированные модели ответов и типизированные исключения по кодам ошибок.
- Постраничная выборка с удобным перебором всех страниц через генератор.
- Чтение остатка лимита из заголовка `Api-Point-Balance`.

## Требования

- PHP >= 7.4
- Расширение `ext-json`
- `guzzlehttp/guzzle` ^7.0

## Установка

```bash
composer require avito-tech/avito-ads-sdk-php
```

## Быстрый старт

```php
use AvitoAds\Client;
use AvitoAds\Configuration;

$config = Configuration::create(
    'ВАШ_CLIENT_ID',
    'ВАШ_CLIENT_SECRET',
    123456789            // accountID — идентификатор рекламного аккаунта
)->production();         // ->sandbox() для песочницы

$client = new Client($config);

// Баланс аккаунта (в рублях)
$balance = $client->account()->getBalance();
echo $balance->getBalance(), ' ₽, бонусы: ', $balance->getBonusBalance(), PHP_EOL;
```

`accountID` — это идентификатор аккаунта, к которому привязан токен; он задаётся один раз в конфигурации и подставляется во все запросы автоматически.

## Конфигурация

Объект `Configuration` неизменяемый: все методы `with*()` возвращают новый экземпляр.

```php
$config = Configuration::create($clientId, $clientSecret, $accountId)
    ->sandbox()                      // или ->production()
    ->withTimeout(30.0)              // таймаут запроса, сек
    ->withConnectTimeout(10.0)       // таймаут соединения, сек
    ->withMaxRetries(4)              // макс. число повторов при 429/5xx
    ->withRetryBaseDelay(1000)       // базовая задержка повтора, мс
    ->withTokenLeeway(60);           // запас на досрочное обновление токена, сек
```

### Окружения

| Окружение | Базовый адрес API |
|---|---|
| `production()` | `https://api.avito.ru/ads/` |
| `sandbox()` | `https://api.avito.ru/ads-sandbox/` |

Эндпоинт получения токена общий для обоих окружений: `https://api.avito.ru/token`.

## Хранение и обновление токена

Токен запрашивается автоматически при первом обращении и переиспользуется до истечения срока. По умолчанию он хранится в памяти процесса. Чтобы переживать перезапуски и переиспользоваться между запросами, передайте PSR-16 кэш:

```php
$config = Configuration::create($clientId, $clientSecret, $accountId)
    ->production()
    ->withCache($psr16Cache); // любой PSR-16 (Symfony Cache, Laravel cache и т.п.)
```

Можно задать и собственное хранилище, реализовав `AvitoAds\Auth\Storage\TokenStorageInterface`, и передать его через `withTokenStorage()`.

## Работа с методами API

Все группы методов доступны через ресурсы клиента.

### Аккаунт

```php
$account = $client->account()->get();
echo $account->getShortName(), ' / ИНН ', $account->getInn(), PHP_EOL;

$balance = $client->account()->getBalance();
```

### Дочерние аккаунты и переводы

```php
$children = $client->childAccounts()->list();
foreach ($children as $child) {
    echo $child->getId(), ' ', $child->getShortName(), PHP_EOL;
}

// Создать дочерний аккаунт-непокупатель
$created = $client->childAccounts()->createNonpayerChild('ООО Дочка', true);

// Перевести средства / бонусы (сумма не менее 1)
$client->childAccounts()->transferFunds($accountIdTo = 987654321, 5000);
$client->childAccounts()->transferBonus($accountIdTo = 987654321, 100);
```

### Рекламодатели

```php
use AvitoAds\Enum\LegalType;
use AvitoAds\Enum\LegalRole;

$created = $client->advertisers()->create(
    inn: '7712345678',           // именованные аргументы доступны на PHP 8+
    shortName: 'ООО Реклама',
    longName: 'Общество с ограниченной ответственностью «Реклама»',
    ogrn: '1177746123456',
    legalAddress: 'г. Москва, ул. Примерная, д. 1',
    actualAddress: 'г. Москва, ул. Примерная, д. 1',
    legalRole: LegalRole::ADVERTISER,
    legalType: LegalType::UL,
    kpp: '771701001'
);

$advertisers = $client->advertisers()->list();
```

На PHP 7.4 используйте позиционные аргументы в том же порядке.

### Договоры

Для создания договора удобно использовать `ContractBuilder` — он проверяет обязательные поля по типу договора и бросает понятное исключение валидации ещё до запроса.

```php
use AvitoAds\Resource\ContractBuilder;
use AvitoAds\Enum\ContractCounterpartyType;

// Посреднический договор
$builder = ContractBuilder::intermediaryContract()
    ->advertiser(987654321)
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
echo 'ID договора: ', $created->getId(), PHP_EOL;
```

Доступны быстрые конструкторы `ContractBuilder::service()`, `ContractBuilder::intermediaryContract()` и `ContractBuilder::external('CID')`. Дополнительное соглашение создаётся указанием `->parentId(...)` (поле `intermediary` при этом передавать нельзя).

В `create()` можно передать и готовый массив тела запроса — тогда клиентская валидация не выполняется.

```php
$contracts = $client->contracts()->list();
```

### Кампании, группы, креативы

```php
$campaigns = $client->campaigns()->list();
foreach ($campaigns as $campaign) {
    echo $campaign->getId(), ' ', $campaign->getName(), ' [', $campaign->getStatus(), "]\n";
}

$groups = $client->groups()->list();
$creatives = $client->creatives()->list();

// Изменение бюджета и ставки группы (только для ручного управления ставкой; значение не менее 1)
$client->groups()->changeBudget($groupId = 555, 100000);
$client->groups()->changePrice($groupId = 555, 25);
```

### Статистика

Период не может превышать 100 дней; даты — в формате `YYYY-MM-DD`. Эти ограничения проверяются на стороне клиента.

```php
$stats = $client->statistics()->campaign($campaignId = 555, '2025-01-01', '2025-01-31');
$total = $stats->getCampaign()->getTotalData();
echo 'Показы: ', $total->getViews(), ', клики: ', $total->getClicks(), PHP_EOL;

$byGroups = $client->statistics()->groups($campaignId, '2025-01-01', '2025-01-31', [101, 102]);
$byCreatives = $client->statistics()->creatives($campaignId, '2025-01-01', '2025-01-31', [201, 202]);
```

### Пользователи

```php
use AvitoAds\Enum\UserRole;

$users = $client->users()->list();
$client->users()->add($userId = 42, UserRole::ADMIN);
$client->users()->setRole($userId = 42, UserRole::VIEWER);
$client->users()->delete($userId = 42);
```

## Постраничная выборка

Методы `list()` возвращают `PaginatedResult` (реализует `Countable` и `IteratorAggregate`).

```php
$result = $client->campaigns()->list($filter = null, $limit = 50, $page = 1);

echo 'Всего: ', $result->getTotal(), PHP_EOL;
echo 'Остаток лимита API: ', $result->getApiPointBalance(), PHP_EOL;

foreach ($result as $campaign) {
    // ...
}

if ($result->hasNextPage()) {
    $next = $client->campaigns()->list(null, 50, 2);
}
```

Чтобы обойти все страницы автоматически, используйте `iterate()` — он возвращает генератор и подгружает страницы по мере необходимости:

```php
foreach ($client->campaigns()->iterate() as $campaign) {
    echo $campaign->getName(), PHP_EOL;
}
```

## Фильтры

Фильтры можно задавать массивом или объектом-фильтром с проверкой имён полей.

```php
use AvitoAds\Model\Filter\CampaignsFilter;
use AvitoAds\Model\Filter\DateRange;
use AvitoAds\Enum\CampaignStatus;

$filter = CampaignsFilter::create()
    ->statuses([CampaignStatus::ACTIVE])
    ->contractIds([10, 20])
    ->createdAt(new DateRange('2026-06-01', '2026-06-30'));

$campaigns = $client->campaigns()->list($filter);
```

## Обработка ошибок

Все исключения реализуют интерфейс `AvitoAds\Exception\AvitoAdsException`.

| HTTP | Исключение |
|---|---|
| 400 | `BadRequestException` |
| 401 | `AuthenticationException` |
| 403 | `AccessDeniedException` |
| 404 | `NotFoundException` |
| 429 | `RateLimitException` (метод `getRetryAfter()`) |
| 5xx | `ServerException` |
| сеть/таймаут | `NetworkException` |

```php
use AvitoAds\Exception\RateLimitException;
use AvitoAds\Exception\ApiException;

try {
    $client->account()->getBalance();
} catch (RateLimitException $e) {
    // превышен лимит запросов
    $retryAfter = $e->getRetryAfter();
} catch (ApiException $e) {
    echo $e->getStatusCode(), ' ', $e->getErrorCode(), ': ', $e->getMessage();
}
```

Ошибки валидации на стороне клиента (неверный период статистики, сумма перевода меньше 1, неизвестная роль и т. п.) бросают `AvitoAds\Exception\ValidationException`.

## Лимиты и повторы

API ограничивает частоту запросов (порядка 500 в минуту). Клиент автоматически повторяет запросы при `429` и `5xx` с экспоненциальной задержкой и учитывает заголовок `Retry-After`. Остаток лимита доступен из заголовка `Api-Point-Balance` (например, через `PaginatedResult::getApiPointBalance()`).

## Интеграция с Laravel

Service Provider обнаруживается автоматически (package discovery). Опубликуйте конфигурацию:

```bash
php artisan vendor:publish --tag=avito-ads-config
```

Задайте переменные окружения в `.env`:

```dotenv
AVITO_ADS_CLIENT_ID=ваш_client_id
AVITO_ADS_CLIENT_SECRET=ваш_client_secret
AVITO_ADS_ACCOUNT_ID=123456789
AVITO_ADS_ENVIRONMENT=production
```

Используйте через контейнер, внедрение зависимостей или фасад:

```php
use AvitoAds\Client;
use AvitoAds\Integration\Laravel\AvitoAdsFacade as AvitoAds;

$balance = app(Client::class)->account()->getBalance();
// или
$balance = AvitoAds::account()->getBalance();
```

По умолчанию для хранения токена используется стандартный кэш Laravel.

## Интеграция с Symfony

Подключите бандл в `config/bundles.php`:

```php
return [
    // ...
    AvitoAds\Integration\Symfony\AvitoAdsBundle::class => ['all' => true],
];
```

Настройте `config/packages/avito_ads.yaml`:

```yaml
avito_ads:
    client_id: '%env(AVITO_ADS_CLIENT_ID)%'
    client_secret: '%env(AVITO_ADS_CLIENT_SECRET)%'
    account_id: '%env(int:AVITO_ADS_ACCOUNT_ID)%'
    environment: 'production'
    # cache_service: 'cache.app'  # необязательный PSR-16 сервис для токена
```

Внедряйте `AvitoAds\Client` в свои сервисы и контроллеры:

```php
use AvitoAds\Client;

public function __construct(private readonly Client $avitoAds) {}
```

> Для регистрации сервисов используется `addMethodCall(..., returnsClone: true)`, доступный в Symfony 5.1+.

## Интеграция с CodeIgniter 4

Скопируйте конфиг `AvitoAds\Integration\CodeIgniter\AvitoAds` в `app/Config/` (или унаследуйтесь от него) и задайте значения через `.env`:

```dotenv
avitoAds.clientId = "ваш_client_id"
avitoAds.clientSecret = "ваш_client_secret"
avitoAds.accountId = 123456789
avitoAds.environment = "production"
```

Зарегистрируйте сервис в `app/Config/Services.php`:

```php
public static function avitoAds(bool $getShared = true): \AvitoAds\Client
{
    return \AvitoAds\Integration\CodeIgniter\Services::avitoAds($getShared);
}
```

И используйте:

```php
$client = service('avitoAds');
$balance = $client->account()->getBalance();
```

## Песочница

Создание тестового аккаунта доступно только в песочнице:

```php
$config = Configuration::create($clientId, $clientSecret, $accountId)->sandbox();
$client = new Client($config);

$created = $client->account()->createSandboxAccount(
    inn: '7712345678',
    shortName: 'Тестовый аккаунт',
    longName: 'Полное наименование',
    ogrn: '1177746123456',
    legalAddress: 'г. Москва, ул. Примерная, д. 1',
    actualAddress: 'г. Москва, ул. Примерная, д. 1',
    contact: ['name' => 'Иван Иванов', 'phone' => '+79001234567']
);
```

## Тестирование

```bash
composer install
composer test          # модульные тесты (PHPUnit)
composer phpstan       # статический анализ
composer cs            # проверка стиля кода (PSR-12)
```

Интеграционные тесты против реальной песочницы по умолчанию пропускаются. Чтобы запустить их:

```bash
AVITO_ADS_RUN_INTEGRATION=1 \
AVITO_ADS_CLIENT_ID=... \
AVITO_ADS_CLIENT_SECRET=... \
AVITO_ADS_ACCOUNT_ID=... \
vendor/bin/phpunit --testsuite integration
```

## Лицензия

[MIT](LICENSE).
