<?php

declare(strict_types=1);

namespace AvitoAds\Integration\Symfony;

use AvitoAds\Integration\Symfony\DependencyInjection\AvitoAdsExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Бандл Symfony для интеграции клиента Авито Реклама.
 *
 * Подключение (config/bundles.php):
 *
 *     return [
 *         // ...
 *         AvitoAds\Integration\Symfony\AvitoAdsBundle::class => ['all' => true],
 *     ];
 */
final class AvitoAdsBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new AvitoAdsExtension();
        }

        return $this->extension;
    }
}
