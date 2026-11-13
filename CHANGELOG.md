# Changelog

Все заметные изменения в этом проекте документируются в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/),
проект придерживается [семантического версионирования](https://semver.org/lang/ru/).

## [1.4.0] — 2026-11-13

Синхронизация с публичным swagger 1.4.0. Версия SDK теперь совпадает с версией публичного API:
`Client::VERSION` и `Client::API_VERSION` равны `info.version` в `openapi.json`.

### Added
- Константы `Client::VERSION` и `Client::API_VERSION`; копия спецификации `openapi.json` в корне
  репозитория; шаг CI `composer check-version` (версия SDK == `API_VERSION` == `openapi.json`).
- User-Agent `avito-ads-sdk-php/<версия>` в запросах к API и в запросе токена; опция
  `Configuration::withUserAgent()` и класс `AvitoAds\Http\UserAgent`.
- `ConflictException` (HTTP 409) — наследник `ApiException`; бросается, в частности, методами
  `GroupResource::changeBudget()` и `changePrice()` при конфликте версий группы.
- Денежные поля в копейках: `Balance::getBalanceKopeks()`, `getBonusBalanceKopeks()`,
  `StatsData::getSpendKopeks()`, `getSpendBonusKopeks()`, `Campaign::getBudgetKopeks()`,
  `Group::getBudgetKopeks()`, `getPriceKopeks()`.
- Кампания: `getBudgetFrequency()` (`daily`/`entirePeriod`/`mixed`, enum `CampaignBudgetFrequency`),
  `getPerformanceAdditionalAgreementId()`.
- Кампания, группа, креатив: `getStatusReasons()` (список кодов, `[]` если причин нет) и
  `getTopStatusReason()` (`""`, если главная причина не определена).
- Статусы кампании `will_launch_soon`, `partially_active`, `will_stop_soon`, `pausing`, `unpausing`;
  статусы креатива `will_launch_soon`, `pausing`, `unpausing`.
- Группа: `getFrequencyRules()` (модель `FrequencyRule`, enum `FrequencyPeriod`),
  `getBudgetFrequency()` (enum `BudgetFrequency`: `daily`/`entirePeriod`), а также `getActions()`,
  `getActivitySchedule()`, `getAudienceType()`, `getFormat()`, `getPace()`, `getPeriodStartAt()`,
  `getPeriodEndAt()`, `getPeriodTimezone()`, `getPriceControl()`, `getScheduleType()`, `getStartAsap()`,
  `getUserId()` и восемь `getTargeting*Ids()`. Новые enum: `GroupFormat`, `AudienceType`, `Paces`,
  `GroupPriceControl`, `ScheduleType`.
- Креатив: `getKktuFields()` (модель `KktuFieldValue`), `getLandingId()`, `getOriginUrl()`,
  `getPreviewUrl()`, `getUserId()`.
- Договор: `isAgentActingForPublisher()`, `isCreativeReporter()`.
- Песочница: `CreatedAccount::getWarnings()`; ответ `207 Multi-Status` (аккаунт создан, часть тестовых
  данных не подготовилась) обрабатывается как успех.
- Модель `CampaignStatistic`: статистика кампании с `getPaymentModel()` и `getCampaignType()`.

### Changed
- Значения перечислений в ответах не проверяются: неизвестные статусы, `budgetFrequency` и коды причин
  возвращаются строками как есть. Проверка по enum — только для входных параметров.
- `Campaign::getBudget()` (как и новый `getBudgetKopeks()`) возвращает `null`, если суммы нет
  (кампания с `budgetFrequency: mixed`), и `0` для пустой кампании.
- `CampaignStatisticResult::getCampaign()` возвращает `CampaignStatistic` (подкласс `EntityStatistic`);
  `EntityStatistic` больше не `final`.

### Deprecated
- `Creative::getLegalInfo()` — поле `legalInfo` устарело в swagger 1.2.0, используйте `getKktuFields()`.
- `Group::getFrequencyCount()` и `getFrequencyPeriod()` — сохраняются для совместимости и соответствуют
  первому правилу, используйте `getFrequencyRules()`.
- `EntityStatistic::getPaymentModel()` и `getCampaignType()` — этих полей нет в статистике групп и
  креативов, используйте `CampaignStatistic`.

### Breaking
- `AccountResource::createSandboxAccount()`: `legalType` обязателен (как в swagger) и проверяется по
  `LegalType`, `actualAddress` стал необязательным. Новый порядок параметров: `inn, shortName, longName,
  ogrn, legalAddress, legalType, contact, actualAddress = null, kpp = null`.
- User-Agent: вместо стандартного UA Guzzle SDK отправляет `avito-ads-sdk-php/<версия>`;
  пользовательский User-Agent теперь дополняется меткой SDK, а не заменяет её.
- Изменения swagger, ломающие клиентов API (методов SDK не затрагивают): из `BudgetFrequency` удалено
  значение `weekly` (1.2.1); входные `frequencyCount`/`frequencyPeriod` при создании группы заменены на
  `frequencyRules`.

## Первая версия (dev-main, без тега)

### Added
- Первый релиз клиента API Авито Реклама.
- Ресурсы: аккаунт и баланс, дочерние аккаунты и переводы средств/бонусов,
  рекламодатели, договоры (с `ContractBuilder`), кампании, группы, креативы,
  статистика, пользователи.
- OAuth2 `client_credentials` с кэшированием и автоматическим обновлением токена.
- Окружения production и sandbox.
- Автоматические повторы при `429`/`5xx` и обновление токена при `401`.
- Типизированные модели и исключения.
- Постраничная выборка и обход всех страниц через `iterate()`.
- Интеграции с Laravel, Symfony и CodeIgniter 4.
- Модульные тесты (PHPUnit) и опциональные интеграционные тесты против песочницы.
