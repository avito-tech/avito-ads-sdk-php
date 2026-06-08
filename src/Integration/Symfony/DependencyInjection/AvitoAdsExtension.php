<?php

declare(strict_types=1);

namespace AvitoAds\Integration\Symfony\DependencyInjection;

use AvitoAds\Client;
use AvitoAds\Configuration as ClientConfiguration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Расширение контейнера: регистрирует сервисы клиента Авито Реклама.
 *
 * Регистрирует два публичных сервиса:
 *  - AvitoAds\Configuration — собранная конфигурация;
 *  - AvitoAds\Client — готовый клиент (его и внедряйте в свои сервисы/контроллеры).
 */
final class AvitoAdsExtension extends Extension
{
    /**
     * @param array<int, array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var array<string, mixed> $config */
        $config = $this->processConfiguration($configuration, $configs);

        // Определение Configuration через цепочку фабричных вызовов.
        $configurationDefinition = new Definition(ClientConfiguration::class);
        $configurationDefinition->setFactory([ClientConfiguration::class, 'create']);
        $configurationDefinition->setArguments([
            (string) $config['client_id'],
            (string) $config['client_secret'],
            (int) $config['account_id'],
        ]);

        $environmentMethod = ($config['environment'] ?? 'production') === 'sandbox' ? 'sandbox' : 'production';
        $configurationDefinition->addMethodCall($environmentMethod, [], true);
        $configurationDefinition->addMethodCall('withTimeout', [(float) $config['timeout']], true);
        $configurationDefinition->addMethodCall('withConnectTimeout', [(float) $config['connect_timeout']], true);
        $configurationDefinition->addMethodCall('withMaxRetries', [(int) $config['max_retries']], true);
        $configurationDefinition->addMethodCall('withRetryBaseDelay', [(int) $config['retry_base_delay_ms']], true);
        $configurationDefinition->addMethodCall('withTokenLeeway', [(int) $config['token_leeway']], true);

        if (!empty($config['cache_service'])) {
            $configurationDefinition->addMethodCall(
                'withCache',
                [new Reference((string) $config['cache_service'])],
                true
            );
        }

        $configurationDefinition->setPublic(true);
        $container->setDefinition(ClientConfiguration::class, $configurationDefinition);

        // Определение Client.
        $clientDefinition = new Definition(Client::class);
        $clientDefinition->setArguments([new Reference(ClientConfiguration::class)]);
        $clientDefinition->setPublic(true);
        $container->setDefinition(Client::class, $clientDefinition);

        $container->setAlias('avito_ads.client', Client::class)->setPublic(true);
    }

    public function getAlias(): string
    {
        return 'avito_ads';
    }
}
