<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit\Resource;

use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\Campaign;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Tests\TestCase;

final class ResourceTest extends TestCase
{
    public function testGetBalanceHitsCorrectEndpoint(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse(),
            $this->jsonResponse(200, ['balance' => 15000, 'bonusBalance' => 500], ['Api-Point-Balance' => '4999']),
        ]);

        $balance = $client->account()->getBalance();
        self::assertSame(15000, $balance->getBalance());
        self::assertSame(500, $balance->getBonusBalance());

        $request = $this->mockHandler->getLastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertStringContainsString('/ads/v1/account/123456789/balance', (string) $request->getUri());
        self::assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
    }

    public function testCampaignsListParsesAndSendsEmptyFilterAsObject(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse(),
            $this->jsonResponse(200, [
                'total' => 2,
                'campaigns' => [
                    ['id' => 1, 'name' => 'Кампания 1', 'status' => 'active'],
                    ['id' => 2, 'name' => 'Кампания 2', 'status' => 'paused'],
                ],
            ]),
        ]);

        $result = $client->campaigns()->list();
        self::assertInstanceOf(PaginatedResult::class, $result);
        self::assertSame(2, $result->getTotal());
        self::assertCount(2, $result);

        $items = $result->getItems();
        self::assertInstanceOf(Campaign::class, $items[0]);
        self::assertSame('Кампания 1', $items[0]->getName());

        $request = $this->mockHandler->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertStringContainsString('/ads/v1/account/123456789/campaigns', (string) $request->getUri());
        // Пустой фильтр должен сериализоваться как объект {} , а не как массив [].
        self::assertStringContainsString('"filter":{}', (string) $request->getBody());
    }

    public function testChangeBudgetRejectsZero(): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->groups()->changeBudget(10, 0);
    }

    public function testTransferFundsRejectsBelowOne(): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->childAccounts()->transferFunds(999, 0);
    }

    public function testStatisticsRejectsPeriodOver100Days(): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->statistics()->campaign(1, '2025-01-01', '2025-06-01');
    }

    /**
     * @dataProvider invalidStatisticsDateProvider
     */
    public function testStatisticsRejectsInvalidDates(string $dateFrom, string $dateTo): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->statistics()->campaign(1, $dateFrom, $dateTo);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function invalidStatisticsDateProvider(): array
    {
        return [
            'impossible day (Feb 31)' => ['2025-02-31', '2025-03-01'],
            'impossible month' => ['2025-13-01', '2025-13-05'],
            'wrong separator' => ['2025/01/01', '2025/01/31'],
            'datetime instead of date' => ['2025-01-01 00:00', '2025-01-31'],
        ];
    }

    public function testStatisticsCampaignParsesResult(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse(),
            $this->jsonResponse(200, [
                'campaign' => [
                    'id' => 1,
                    'name' => 'Промо',
                    'paymentModel' => 'CPM',
                    'campaignType' => 'textImage',
                    'data' => [['timestamp' => '2025-01-01', 'views' => 100, 'clicks' => 5]],
                    'totalData' => ['views' => 100, 'clicks' => 5],
                ],
                'groups' => [],
                'creatives' => [],
            ]),
        ]);

        $result = $client->statistics()->campaign(1, '2025-01-01', '2025-01-31');
        self::assertSame('Промо', $result->getCampaign()->getName());
        self::assertSame(100, $result->getCampaign()->getTotalData()->getViews());

        $request = $this->mockHandler->getLastRequest();
        self::assertStringContainsString('/campaigns/1/stats', (string) $request->getUri());
        self::assertStringContainsString('"dateFrom":"2025-01-01"', (string) $request->getBody());
    }

    public function testUsersListParsed(): void
    {
        $client = $this->makeClient([
            $this->tokenResponse(),
            $this->jsonResponse(200, [
                'total' => 1,
                'users' => [['id' => 42, 'role' => 'admin', 'hasLoggedIn' => true]],
            ]),
        ]);

        $users = $client->users()->list();
        self::assertCount(1, $users);
        self::assertSame(42, $users[0]->getId());
        self::assertSame('admin', $users[0]->getRole());
        self::assertTrue($users[0]->getHasLoggedIn());
    }

    public function testAddUserRejectsInvalidRole(): void
    {
        $client = $this->makeClient([$this->tokenResponse()]);
        $this->expectException(ValidationException::class);
        $client->users()->add(42, 'superhero');
    }
}
