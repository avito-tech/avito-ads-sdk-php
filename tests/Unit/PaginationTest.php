<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    public function testPaginatedResultBasics(): void
    {
        $result = new PaginatedResult(3, ['a', 'b'], 1, 2, 100);
        self::assertSame(3, $result->getTotal());
        self::assertCount(2, $result);
        self::assertTrue($result->hasNextPage());
        self::assertSame(100, $result->getApiPointBalance());
        self::assertSame(['a', 'b'], iterator_to_array($result));
    }

    public function testHasNextPageFalseOnLastPage(): void
    {
        $result = new PaginatedResult(3, ['c'], 2, 2, null);
        self::assertFalse($result->hasNextPage());
    }

    public function testIteratorWalksAllPages(): void
    {
        $served = [];
        $loader = static function (ListRequest $req) use (&$served): PaginatedResult {
            $served[] = $req->getPage();
            return $req->getPage() === 1
                ? new PaginatedResult(3, ['a', 'b'], 1, 2, 100)
                : new PaginatedResult(3, ['c'], 2, 2, 99);
        };

        $items = iterator_to_array(ResultIterator::iterate($loader, new ListRequest([], 2, 1)), false);
        self::assertSame(['a', 'b', 'c'], $items);
        self::assertSame([1, 2], $served);
    }

    public function testIteratorStopsOnEmptyPage(): void
    {
        $loader = static function (ListRequest $req): PaginatedResult {
            return new PaginatedResult(0, [], $req->getPage(), 20, 100);
        };
        $items = iterator_to_array(ResultIterator::iterate($loader, new ListRequest([], 20, 1)), false);
        self::assertSame([], $items);
    }
}
