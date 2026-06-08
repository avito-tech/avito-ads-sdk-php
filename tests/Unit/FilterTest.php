<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Enum\CampaignStatus;
use AvitoAds\Model\Filter\CampaignsFilter;
use AvitoAds\Model\Filter\CreativesFilter;
use AvitoAds\Model\Filter\DateRange;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function testCampaignsFilterBuildsArray(): void
    {
        $filter = CampaignsFilter::create()
            ->ids([1, 2])
            ->statuses([CampaignStatus::ACTIVE, CampaignStatus::PAUSED])
            ->contractIds([10, 20])
            ->createdAt(new DateRange('2025-01-01', '2025-01-31'));

        $array = $filter->toArray();
        self::assertSame([1, 2], $array['ids']);
        self::assertSame(['active', 'paused'], $array['statuses']);
        self::assertSame([10, 20], $array['contractIDs']);
        self::assertSame(['from' => '2025-01-01', 'to' => '2025-01-31'], $array['createdAt']);
    }

    public function testCreativesFilterRenamesKeys(): void
    {
        $filter = CreativesFilter::create()
            ->groupIds([5])
            ->campaignIds([7]);
        $array = $filter->toArray();
        self::assertSame([5], $array['groupIDs']);
        self::assertSame([7], $array['campaignIDs']);
    }

    public function testEmptyFilterIsEmptyArray(): void
    {
        self::assertSame([], CampaignsFilter::create()->toArray());
    }
}
