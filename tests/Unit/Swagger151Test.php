<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Enum\ConversionGoalType;
use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\Conversions;
use AvitoAds\Model\StatsData;
use AvitoAds\Tests\TestCase;

/**
 * Изменения swagger 1.5.1: конверсии и лимит ID в статистике.
 */
final class Swagger151Test extends TestCase
{
    private const FROM = '2026-11-01';
    private const TO = '2026-11-10';

    public function testClickInConversions(): void
    {
        $data = StatsData::fromArray(['conversions' => [
            'contacts' => [
                'totalCount' => 5, 'webCount' => 2, 'mavCount' => 1, 'androidCount' => 1, 'iosCount' => 1, 'cr' => 0.05,
            ],
            'subscriptions' => [
                'totalCount' => 0, 'webCount' => 0, 'mavCount' => 0, 'androidCount' => 0, 'iosCount' => 0, 'cr' => 0,
            ],
            'cpaKopeks' => 0,
            'goalType' => 'contacts',
            'cr' => 0.05,
        ]]);

        $conversions = $data->getConversions();
        self::assertInstanceOf(Conversions::class, $conversions);
        $contacts = $conversions->getContacts();
        self::assertNotNull($contacts);
        self::assertSame(5, $contacts->getTotalCount());
        self::assertSame(2, $contacts->getWebCount());
        self::assertSame(1, $contacts->getMavCount());
        self::assertSame(1, $contacts->getAndroidCount());
        self::assertSame(1, $contacts->getIosCount());
        self::assertSame(0.05, $contacts->getCr());
        $subscriptions = $conversions->getSubscriptions();
        self::assertNotNull($subscriptions);
        self::assertSame(0, $subscriptions->getTotalCount());
        self::assertSame(0, $conversions->getCpaKopeks());
        self::assertSame(0.05, $conversions->getCr());
        self::assertSame(ConversionGoalType::CONTACTS, $conversions->getGoalType());
        self::assertNull($conversions->getGoalHits());
        self::assertNull($conversions->getCustomGoal());
    }

    public function testClickOutConversions(): void
    {
        $conversions = Conversions::fromArray([
            'goalHits' => 10,
            'cpaKopeks' => 1250,
            'goalType' => 'customGoal',
            'customGoal' => ['id' => 42, 'name' => 'Заявка'],
            'cr' => 0.05,
        ]);
        self::assertSame(10, $conversions->getGoalHits());
        self::assertSame(1250, $conversions->getCpaKopeks());
        self::assertSame(ConversionGoalType::CUSTOM_GOAL, $conversions->getGoalType());
        $goal = $conversions->getCustomGoal();
        self::assertNotNull($goal);
        self::assertSame(42, $goal->getId());
        self::assertSame('Заявка', $goal->getName());
        self::assertNull($conversions->getContacts());
        self::assertNull($conversions->getSubscriptions());
    }

    public function testAbsentConversionsAndUnknownGoalType(): void
    {
        self::assertNull(StatsData::fromArray([])->getConversions());
        self::assertNull(StatsData::fromArray(['conversions' => null])->getConversions());

        $unknown = Conversions::fromArray(['goalType' => 'brand_new']);
        self::assertSame('brand_new', $unknown->getGoalType());
        self::assertNull($unknown->getCr());
        self::assertNull($unknown->getCpaKopeks());
        self::assertSame(
            ['contacts', 'subscriptions', 'customGoal'],
            ConversionGoalType::all()
        );
    }

    public function testIncludeConversionsFlagIsSentOnlyWhenRequested(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse(),
            $this->jsonResponse(200, ['campaign' => [], 'groups' => [], 'creatives' => []]),
            $this->jsonResponse(200, ['campaign' => [], 'groups' => [], 'creatives' => []]),
            $this->jsonResponse(200, ['groups' => []]),
            $this->jsonResponse(200, ['groups' => []]),
            $this->jsonResponse(200, ['creatives' => []]),
        ]);

        $client->statistics()->campaign(1, self::FROM, self::TO, true);
        self::assertSame(
            ['dateFrom' => self::FROM, 'dateTo' => self::TO, 'includeConversions' => true],
            $this->lastBody()
        );
        $client->statistics()->campaign(1, self::FROM, self::TO);
        self::assertSame(['dateFrom' => self::FROM, 'dateTo' => self::TO], $this->lastBody());

        $client->statistics()->groups(1, self::FROM, self::TO, [2], true);
        self::assertSame(
            ['dateFrom' => self::FROM, 'dateTo' => self::TO, 'groupIDs' => [2], 'includeConversions' => true],
            $this->lastBody()
        );
        $client->statistics()->groups(1, self::FROM, self::TO, [2]);
        self::assertSame(['dateFrom' => self::FROM, 'dateTo' => self::TO, 'groupIDs' => [2]], $this->lastBody());

        $client->statistics()->creatives(1, self::FROM, self::TO, [3]);
        self::assertSame(['dateFrom' => self::FROM, 'dateTo' => self::TO, 'creativeIDs' => [3]], $this->lastBody());
    }

    /**
     * @dataProvider idsMethodProvider
     */
    public function testIdsLimitAccepts5000(string $method): void
    {
        $client = $this->makeClient([$this->tokenResponse(), $this->jsonResponse(200, [])]);
        self::assertSame([], $client->statistics()->{$method}(1, self::FROM, self::TO, range(1, 5000)));
    }

    /**
     * @dataProvider invalidIdsProvider
     * @param int[] $ids
     */
    public function testIdsLimitRejectsEmptyAndOver5000(string $method, array $ids): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->statistics()->{$method}(1, self::FROM, self::TO, $ids);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function idsMethodProvider(): array
    {
        return ['groups' => ['groups'], 'creatives' => ['creatives']];
    }

    /**
     * @return array<string, array{0: string, 1: int[]}>
     */
    public function invalidIdsProvider(): array
    {
        return [
            'groups: empty' => ['groups', []],
            'groups: 5001' => ['groups', range(1, 5001)],
            'creatives: empty' => ['creatives', []],
            'creatives: 5001' => ['creatives', range(1, 5001)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function lastBody(): array
    {
        $request = $this->mockHandler->getLastRequest();
        self::assertNotNull($request);

        return json_decode((string) $request->getBody(), true);
    }
}
