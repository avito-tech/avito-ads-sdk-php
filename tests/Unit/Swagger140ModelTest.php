<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Enum\BudgetFrequency;
use AvitoAds\Enum\CampaignBudgetFrequency;
use AvitoAds\Enum\FrequencyPeriod;
use AvitoAds\Model\Balance;
use AvitoAds\Model\Campaign;
use AvitoAds\Model\CampaignStatistic;
use AvitoAds\Model\CampaignStatisticResult;
use AvitoAds\Model\Contract;
use AvitoAds\Model\CreatedAccount;
use AvitoAds\Model\Creative;
use AvitoAds\Model\FrequencyRule;
use AvitoAds\Model\Group;
use AvitoAds\Model\KktuFieldValue;
use AvitoAds\Model\StatsData;
use PHPUnit\Framework\TestCase;

/**
 * Поля моделей, добавленные при синхронизации со swagger 1.4.0.
 */
final class Swagger140ModelTest extends TestCase
{
    public function testKopeksFields(): void
    {
        $balance = Balance::fromArray([
            'balance' => 150,
            'balanceKopeks' => 15012,
            'bonusBalance' => 5,
            'bonusBalanceKopeks' => 550,
        ]);
        self::assertSame(15012, $balance->getBalanceKopeks());
        self::assertSame(550, $balance->getBonusBalanceKopeks());

        $stats = StatsData::fromArray(['spend' => 10, 'spendKopeks' => 1050, 'spendBonusKopeks' => 7]);
        self::assertSame(1050, $stats->getSpendKopeks());
        self::assertSame(7, $stats->getSpendBonusKopeks());

        $group = Group::fromArray(['budget' => 1000, 'budgetKopeks' => 100000, 'price' => 25, 'priceKopeks' => 2550]);
        self::assertSame(100000, $group->getBudgetKopeks());
        self::assertSame(2550, $group->getPriceKopeks());
    }

    public function testCampaignBudgetAbsentIsNullNotZero(): void
    {
        $mixed = Campaign::fromArray(['budgetFrequency' => CampaignBudgetFrequency::MIXED]);
        self::assertNull($mixed->getBudget());
        self::assertNull($mixed->getBudgetKopeks());
        self::assertSame('mixed', $mixed->getBudgetFrequency());

        $nullBudget = Campaign::fromArray(['budget' => null, 'budgetKopeks' => null]);
        self::assertNull($nullBudget->getBudget());
        self::assertNull($nullBudget->getBudgetKopeks());

        $empty = Campaign::fromArray(['budget' => 0, 'budgetKopeks' => 0, 'budgetFrequency' => 'entirePeriod']);
        self::assertSame(0, $empty->getBudget());
        self::assertSame(0, $empty->getBudgetKopeks());
    }

    public function testCampaignNewFields(): void
    {
        $campaign = Campaign::fromArray([
            'status' => 'partially_active',
            'statusReasons' => ['manual', 'insufficient_funds'],
            'topStatusReason' => 'insufficient_funds',
            'performanceAdditionalAgreementID' => 77,
        ]);
        self::assertSame('partially_active', $campaign->getStatus());
        self::assertSame(['manual', 'insufficient_funds'], $campaign->getStatusReasons());
        self::assertSame('insufficient_funds', $campaign->getTopStatusReason());
        self::assertSame(77, $campaign->getPerformanceAdditionalAgreementId());
    }

    public function testEmptyStatusReasons(): void
    {
        $campaign = Campaign::fromArray(['statusReasons' => [], 'topStatusReason' => '']);
        self::assertSame([], $campaign->getStatusReasons());
        self::assertSame('', $campaign->getTopStatusReason());

        // Поля нет в ответе — те же значения, без ошибки.
        $creative = Creative::fromArray([]);
        self::assertSame([], $creative->getStatusReasons());
        self::assertSame('', $creative->getTopStatusReason());
    }

    /**
     * Политика: значения перечислений в ответах не проверяются, неизвестные возвращаются как есть.
     */
    public function testUnknownEnumValuesInResponsesAreKept(): void
    {
        $campaign = Campaign::fromArray([
            'status' => 'brand_new_status',
            'budgetFrequency' => 'quarterly',
            'statusReasons' => ['brand_new_reason'],
            'topStatusReason' => 'brand_new_reason',
        ]);
        self::assertSame('brand_new_status', $campaign->getStatus());
        self::assertSame('quarterly', $campaign->getBudgetFrequency());
        self::assertSame(['brand_new_reason'], $campaign->getStatusReasons());
        self::assertSame('brand_new_reason', $campaign->getTopStatusReason());

        $group = Group::fromArray([
            'status' => 'brand_new_status',
            'budgetFrequency' => 'weekly',
            'statusReasons' => ['brand_new_reason'],
            'frequencyRules' => [['count' => 3, 'period' => 'quarter']],
        ]);
        self::assertSame('brand_new_status', $group->getStatus());
        self::assertSame('weekly', $group->getBudgetFrequency());
        self::assertSame(['brand_new_reason'], $group->getStatusReasons());
        self::assertSame('quarter', $group->getFrequencyRules()[0]->getPeriod());

        $creative = Creative::fromArray(['status' => 'brand_new_status', 'statusReasons' => ['x']]);
        self::assertSame('brand_new_status', $creative->getStatus());
        self::assertSame(['x'], $creative->getStatusReasons());
    }

    public function testGroupFrequencyRulesAndDeprecatedFields(): void
    {
        $group = Group::fromArray([
            'frequencyRules' => [
                ['count' => 3, 'period' => FrequencyPeriod::DAY],
                ['count' => 10, 'period' => FrequencyPeriod::WEEK],
            ],
            'frequencyCount' => 3,
            'frequencyPeriod' => 'day',
            'budgetFrequency' => BudgetFrequency::DAILY,
        ]);
        $rules = $group->getFrequencyRules();
        self::assertCount(2, $rules);
        self::assertInstanceOf(FrequencyRule::class, $rules[0]);
        self::assertSame(3, $rules[0]->getCount());
        self::assertSame('day', $rules[0]->getPeriod());
        self::assertSame(10, $rules[1]->getCount());
        self::assertSame('daily', $group->getBudgetFrequency());

        // Устаревшие поля остаются доступны.
        self::assertSame(3, $group->getFrequencyCount());
        self::assertSame('day', $group->getFrequencyPeriod());

        self::assertSame([], Group::fromArray([])->getFrequencyRules());
    }

    public function testGroupRemainingFields(): void
    {
        $group = Group::fromArray([
            'actions' => ['pause', 'edit'],
            'activitySchedule' => ['monday' => [9, 10, 11], 'sunday' => []],
            'audienceType' => 'autotargeting',
            'format' => 'native',
            'pace' => 'even',
            'periodStartAt' => '2026-11-01T00:00:00Z',
            'periodEndAt' => null,
            'periodTimezone' => 'Europe/Moscow',
            'priceControl' => 'manual',
            'scheduleType' => 'custom',
            'startASAP' => false,
            'userID' => 42,
            'targetingAgeIDs' => [1, 2],
            'targetingAudienceIDs' => ['aud-1'],
            'targetingFamilyStatusIDs' => [3],
            'targetingGenderIDs' => [1],
            'targetingIncomeIDs' => [4],
            'targetingInterestIDs' => ['int-1', 'int-2'],
            'targetingLocationIDs' => [637640],
            'targetingParentStatusIDs' => [5],
        ]);
        self::assertSame(['pause', 'edit'], $group->getActions());
        self::assertSame(['monday' => [9, 10, 11], 'sunday' => []], $group->getActivitySchedule());
        self::assertSame('autotargeting', $group->getAudienceType());
        self::assertSame('native', $group->getFormat());
        self::assertSame('even', $group->getPace());
        self::assertSame('2026-11-01T00:00:00Z', $group->getPeriodStartAt());
        self::assertNull($group->getPeriodEndAt());
        self::assertSame('Europe/Moscow', $group->getPeriodTimezone());
        self::assertSame('manual', $group->getPriceControl());
        self::assertSame('custom', $group->getScheduleType());
        self::assertFalse($group->getStartAsap());
        self::assertSame(42, $group->getUserId());
        self::assertSame([1, 2], $group->getTargetingAgeIds());
        self::assertSame(['aud-1'], $group->getTargetingAudienceIds());
        self::assertSame([3], $group->getTargetingFamilyStatusIds());
        self::assertSame([1], $group->getTargetingGenderIds());
        self::assertSame([4], $group->getTargetingIncomeIds());
        self::assertSame(['int-1', 'int-2'], $group->getTargetingInterestIds());
        self::assertSame([637640], $group->getTargetingLocationIds());
        self::assertSame([5], $group->getTargetingParentStatusIds());
    }

    public function testCreativeNewFieldsAndLegacyLegalInfo(): void
    {
        $creative = Creative::fromArray([
            'kktuFields' => [['key' => 'kktu', 'value' => '30.10.1'], ['key' => 'age', 'value' => '18']],
            'landingID' => 5,
            'originUrl' => 'https://example.com/?utm=1',
            'previewUrl' => 'https://avito.ru/preview/1',
            'userID' => 42,
            'legalInfo' => ['kktu' => '30.10.1'],
        ]);
        $fields = $creative->getKktuFields();
        self::assertCount(2, $fields);
        self::assertInstanceOf(KktuFieldValue::class, $fields[0]);
        self::assertSame('kktu', $fields[0]->getKey());
        self::assertSame('30.10.1', $fields[0]->getValue());
        self::assertSame(5, $creative->getLandingId());
        self::assertSame('https://example.com/?utm=1', $creative->getOriginUrl());
        self::assertSame('https://avito.ru/preview/1', $creative->getPreviewUrl());
        self::assertSame(42, $creative->getUserId());
        // Устаревшее поле сохраняется.
        self::assertSame(['kktu' => '30.10.1'], $creative->getLegalInfo());
    }

    public function testContractFlags(): void
    {
        $contract = Contract::fromArray(['isAgentActingForPublisher' => true, 'isCreativeReporter' => null]);
        self::assertTrue($contract->isAgentActingForPublisher());
        self::assertNull($contract->isCreativeReporter());
    }

    public function testCreatedAccountWarnings(): void
    {
        $created = CreatedAccount::fromArray(['accountID' => 9, 'warnings' => ['campaigns not prepared']]);
        self::assertSame(9, $created->getAccountId());
        self::assertSame(['campaigns not prepared'], $created->getWarnings());
        self::assertSame([], CreatedAccount::fromArray(['accountID' => 9, 'warnings' => []])->getWarnings());
    }

    public function testCampaignStatisticHasPaymentModel(): void
    {
        $result = CampaignStatisticResult::fromArray([
            'campaign' => ['id' => 1, 'paymentModel' => 'CPM', 'campaignType' => 'textImage'],
        ]);
        $campaign = $result->getCampaign();
        self::assertInstanceOf(CampaignStatistic::class, $campaign);
        self::assertSame('CPM', $campaign->getPaymentModel());
        self::assertSame('textImage', $campaign->getCampaignType());
    }
}
