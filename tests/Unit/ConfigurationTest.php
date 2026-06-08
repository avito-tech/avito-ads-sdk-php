<?php

declare(strict_types=1);

namespace AvitoAds\Tests\Unit;

use AvitoAds\Configuration;
use AvitoAds\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testBaseUris(): void
    {
        $config = Configuration::create('id', 'secret', 123)->production();
        self::assertSame('https://api.avito.ru/ads/', $config->getBaseUri());
        self::assertSame('https://api.avito.ru/token', $config->getTokenUrl());
        self::assertSame('https://api.avito.ru/ads-sandbox/', $config->sandbox()->getBaseUri());
    }

    public function testImmutability(): void
    {
        $config = Configuration::create('id', 'secret', 123);
        $changed = $config->withMaxRetries(9);
        self::assertSame(9, $changed->getMaxRetries());
        self::assertSame(4, $config->getMaxRetries());
        self::assertNotSame($config, $changed);
    }

    public function testRejectsNonPositiveAccountId(): void
    {
        $this->expectException(ConfigurationException::class);
        Configuration::create('id', 'secret', 0);
    }

    public function testRejectsEmptyCredentials(): void
    {
        $this->expectException(ConfigurationException::class);
        Configuration::create('', 'secret', 123);
    }
}
