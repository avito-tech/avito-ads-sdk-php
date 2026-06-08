<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\Pagination\ListRequest;
use PHPUnit\Framework\TestCase;

final class ListRequestTest extends TestCase
{
    public function testEmptyFilterSerializesAsJsonObject(): void
    {
        $request = new ListRequest([], 20, 1);
        $array = $request->toArray();
        self::assertInstanceOf(\stdClass::class, $array['filter']);
        self::assertSame('{}', json_encode($array['filter']));
    }

    public function testNonEmptyFilterKept(): void
    {
        $request = new ListRequest(['ids' => [1, 2]], 50, 2);
        self::assertSame(['ids' => [1, 2]], $request->toArray()['filter']);
        self::assertSame(50, $request->getLimit());
        self::assertSame(2, $request->getPage());
    }

    public function testWithPageClones(): void
    {
        $request = new ListRequest([], 20, 1);
        self::assertSame(5, $request->withPage(5)->getPage());
        self::assertSame(1, $request->getPage());
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testValidation(int $limit, int $page): void
    {
        $this->expectException(ValidationException::class);
        new ListRequest([], $limit, $page);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public function invalidProvider(): array
    {
        return [
            'limit too small' => [0, 1],
            'limit too large' => [101, 1],
            'page too small' => [20, 0],
        ];
    }
}
