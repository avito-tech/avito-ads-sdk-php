<?php

declare(strict_types=1);

namespace AvitoAds\Integration\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Дерево конфигурации бандла (ключ avito_ads).
 *
 * Пример config/packages/avito_ads.yaml:
 *
 *     avito_ads:
 *         client_id: '%env(AVITO_ADS_CLIENT_ID)%'
 *         client_secret: '%env(AVITO_ADS_CLIENT_SECRET)%'
 *         account_id: '%env(int:AVITO_ADS_ACCOUNT_ID)%'
 *         environment: 'production'
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('avito_ads');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('client_id')->defaultValue('')->end()
                ->scalarNode('client_secret')->defaultValue('')->end()
                ->integerNode('account_id')->defaultValue(0)->end()
                ->enumNode('environment')
                    ->values(['production', 'sandbox'])
                    ->defaultValue('production')
                ->end()
                ->floatNode('timeout')->defaultValue(30.0)->end()
                ->floatNode('connect_timeout')->defaultValue(10.0)->end()
                ->integerNode('max_retries')->defaultValue(4)->end()
                ->integerNode('retry_base_delay_ms')->defaultValue(1000)->end()
                ->integerNode('token_leeway')->defaultValue(60)->end()
                ->scalarNode('cache_service')
                    ->info('ID PSR-16 сервиса кэша для хранения токена (необязательно).')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
