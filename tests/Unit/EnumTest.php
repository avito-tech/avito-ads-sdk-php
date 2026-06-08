<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Enum\CampaignStatus;
use AvitoAds\Enum\ContractType;
use AvitoAds\Enum\LegalType;
use AvitoAds\Enum\UserRole;
use AvitoAds\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    public function testAllReturnsValues(): void
    {
        self::assertSame(['ul', 'ip'], LegalType::all());
        self::assertSame(['admin', 'viewer'], UserRole::all());
    }

    public function testIsValid(): void
    {
        self::assertTrue(CampaignStatus::isValid('active'));
        self::assertFalse(CampaignStatus::isValid('unknown'));
    }

    public function testAssertValidThrowsOnInvalid(): void
    {
        $this->expectException(ValidationException::class);
        ContractType::assertValid('nope');
    }

    public function testAssertValidPassesOnValid(): void
    {
        ContractType::assertValid('service');
        $this->addToAssertionCount(1);
    }
}
