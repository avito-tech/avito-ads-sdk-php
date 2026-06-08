<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Enum\ContractCounterpartyType;
use AvitoAds\Exception\ValidationException;
use AvitoAds\Resource\ContractBuilder;
use PHPUnit\Framework\TestCase;

final class ContractBuilderTest extends TestCase
{
    public function testValidServiceContract(): void
    {
        $body = ContractBuilder::service()
            ->advertiser(987)
            ->counterpartyType(ContractCounterpartyType::DIRECT_WITH_ADVERTISER)
            ->subject('distribution')
            ->reportingRequired(true)
            ->date('2025-01-01')
            ->number('A-1')
            ->intermediary(['inn' => '7712345678'])
            ->build();

        self::assertSame('service', $body['type']);
        self::assertSame('direct_with_advertiser', $body['description']);
        self::assertSame('A-1', $body['number']);
        self::assertArrayHasKey('intermediary', $body);
    }

    public function testValidExternalContract(): void
    {
        $body = ContractBuilder::external('EXT-1')
            ->advertiser(1)
            ->counterpartyType('advertiser_intermediary')
            ->intermediary(['inn' => '1'])
            ->build();

        self::assertSame('external', $body['type']);
        self::assertSame('EXT-1', $body['cid']);
    }

    public function testAdditionalAgreementWithParentId(): void
    {
        $body = ContractBuilder::intermediaryContract()
            ->advertiser(1)
            ->counterpartyType('direct_with_advertiser')
            ->subject('mediation')
            ->object('commercial')
            ->reportingRequired(true)
            ->fundsAllocationToPrincipal(true)
            ->date('2025-03-01')
            ->number('DS-1')
            ->parentId(100)
            ->build();

        self::assertSame(100, $body['parentId']);
        self::assertArrayNotHasKey('intermediary', $body);
    }

    public function testServiceMissingDateThrows(): void
    {
        $this->expectException(ValidationException::class);
        ContractBuilder::service()
            ->advertiser(1)
            ->counterpartyType('direct_with_advertiser')
            ->subject('distribution')
            ->reportingRequired(true)
            ->number('A-1')
            ->intermediary(['inn' => '1'])
            ->build();
    }

    public function testExternalWithParentIdThrows(): void
    {
        $this->expectException(ValidationException::class);
        ContractBuilder::external('EXT-1')
            ->advertiser(1)
            ->counterpartyType('advertiser_intermediary')
            ->parentId(5)
            ->build();
    }

    public function testIntermediaryMissingObjectThrows(): void
    {
        $this->expectException(ValidationException::class);
        ContractBuilder::intermediaryContract()
            ->advertiser(1)
            ->counterpartyType('direct_with_advertiser')
            ->subject('mediation')
            ->reportingRequired(true)
            ->date('2025-01-01')
            ->number('N-1')
            ->intermediary(['inn' => '1'])
            ->build();
    }

    public function testParentIdWithIntermediaryThrows(): void
    {
        $this->expectException(ValidationException::class);
        ContractBuilder::intermediaryContract()
            ->advertiser(1)
            ->counterpartyType('direct_with_advertiser')
            ->subject('mediation')
            ->object('commercial')
            ->reportingRequired(true)
            ->fundsAllocationToPrincipal(false)
            ->date('2025-01-01')
            ->number('N-1')
            ->parentId(7)
            ->intermediary(['inn' => '1'])
            ->build();
    }

    public function testMissingIntermediaryWithoutParentThrows(): void
    {
        $this->expectException(ValidationException::class);
        ContractBuilder::service()
            ->advertiser(1)
            ->counterpartyType('direct_with_advertiser')
            ->subject('distribution')
            ->reportingRequired(true)
            ->date('2025-01-01')
            ->number('A-1')
            ->build();
    }
}
