<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Model\Account;
use AvitoAds\Model\Balance;
use AvitoAds\Model\CampaignStatisticResult;
use AvitoAds\Model\ChildAccount;
use AvitoAds\Model\Contract;
use AvitoAds\Model\StatsData;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function testAccountGetters(): void
    {
        $account = Account::fromArray([
            'inn' => '7700000000',
            'shortName' => 'ООО Ромашка',
            'contact' => ['name' => 'Иван', 'phone' => '+7...'],
        ]);
        self::assertSame('7700000000', $account->getInn());
        self::assertSame('ООО Ромашка', $account->getShortName());
        self::assertSame(['name' => 'Иван', 'phone' => '+7...'], $account->getContact());
        self::assertNull($account->getKpp());
    }

    public function testBalanceCastsToInt(): void
    {
        $balance = Balance::fromArray(['balance' => '15000', 'bonusBalance' => 500]);
        self::assertSame(15000, $balance->getBalance());
        self::assertSame(500, $balance->getBonusBalance());
    }

    public function testContractNestedAdvertisers(): void
    {
        $contract = Contract::fromArray([
            'id' => 10,
            'client' => ['id' => 1, 'shortName' => 'Клиент'],
            'contractor' => ['id' => 2, 'shortName' => 'Исполнитель'],
        ]);
        self::assertSame('Клиент', $contract->getClient()->getShortName());
        self::assertSame('Исполнитель', $contract->getContractor()->getShortName());
    }

    public function testChildAccountBalance(): void
    {
        $child = ChildAccount::fromArray([
            'account' => ['id' => 55, 'shortName' => 'Дочерний'],
            'balance' => ['balance' => 1000, 'bonusBalance' => 0],
        ]);
        self::assertSame(55, $child->getId());
        self::assertSame('Дочерний', $child->getShortName());
        self::assertSame(1000, $child->getBalance()->getBalance());
    }

    public function testCampaignStatisticResult(): void
    {
        $result = CampaignStatisticResult::fromArray([
            'campaign' => [
                'id' => 1,
                'name' => 'Промо',
                'data' => [['views' => 100, 'clicks' => 10]],
                'totalData' => ['views' => 100, 'clicks' => 10],
            ],
            'groups' => [['id' => 2, 'name' => 'Группа', 'data' => [], 'totalData' => []]],
            'creatives' => [],
        ]);
        self::assertSame('Промо', $result->getCampaign()->getName());
        $data = $result->getCampaign()->getData();
        self::assertCount(1, $data);
        self::assertInstanceOf(StatsData::class, $data[0]);
        self::assertSame(100, $data[0]->getViews());
        self::assertCount(1, $result->getGroups());
        self::assertSame('Группа', $result->getGroups()[0]->getName());
    }
}
