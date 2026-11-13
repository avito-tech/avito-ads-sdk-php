<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Client;
use PHPUnit\Framework\TestCase;

/**
 * Версия SDK = версия публичного API = info.version в openapi.json.
 */
final class VersionTest extends TestCase
{
    public function testVersionMatchesOpenApi(): void
    {
        $spec = json_decode((string) file_get_contents(__DIR__ . '/../../openapi.json'), true);
        self::assertIsArray($spec);

        self::assertSame($spec['info']['version'], Client::API_VERSION);
        self::assertSame(Client::API_VERSION, Client::VERSION);
    }
}
