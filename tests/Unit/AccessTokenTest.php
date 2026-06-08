<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Auth\AccessToken;
use PHPUnit\Framework\TestCase;

final class AccessTokenTest extends TestCase
{
    public function testFromResponseComputesExpiry(): void
    {
        $token = AccessToken::fromResponse(
            ['access_token' => 'abc', 'expires_in' => 86400, 'token_type' => 'Bearer'],
            1000
        );
        self::assertSame(1000 + 86400, $token->getExpiresAt());
        self::assertSame('Bearer abc', $token->getAuthorizationHeader());
    }

    public function testIsExpiredRespectsLeeway(): void
    {
        $token = new AccessToken('abc', 1000 + 86400, 'Bearer');
        self::assertFalse($token->isExpired(60, 1000));
        self::assertTrue($token->isExpired(60, 1000 + 86400 - 30));
        self::assertTrue($token->isExpired(0, 1000 + 86400));
    }

    public function testArrayRoundTrip(): void
    {
        $token = new AccessToken('abc', 99999, 'Bearer');
        $restored = AccessToken::fromArray($token->toArray());
        self::assertSame('abc', $restored->getToken());
        self::assertSame(99999, $restored->getExpiresAt());
        self::assertSame('Bearer', $restored->getTokenType());
    }
}
