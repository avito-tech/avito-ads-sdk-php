<?php

declare(strict_types=1);

namespace AvitoAds\Integration\Laravel;

use AvitoAds\Client;
use AvitoAds\Configuration;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\CacheInterface;

/**
 * Service Provider для Laravel.
 *
 * Регистрирует синглтоны Configuration и Client на основе config/avito-ads.php,
 * а также публикует файл конфигурации. Обнаруживается автоматически через
 * package discovery (см. секцию extra.laravel в composer.json).
 */
final class AvitoAdsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'avito-ads');

        $this->app->singleton(Configuration::class, static function (Container $app): Configuration {
            /** @var array<string, mixed> $config */
            $config = $app['config']['avito-ads'];

            $configuration = Configuration::create(
                (string) ($config['client_id'] ?? ''),
                (string) ($config['client_secret'] ?? ''),
                (int) ($config['account_id'] ?? 0)
            );

            $configuration = ($config['environment'] ?? 'production') === 'sandbox'
                ? $configuration->sandbox()
                : $configuration->production();

            $configuration = $configuration
                ->withTimeout((float) ($config['timeout'] ?? 30))
                ->withConnectTimeout((float) ($config['connect_timeout'] ?? 10))
                ->withMaxRetries((int) ($config['max_retries'] ?? 4))
                ->withRetryBaseDelay((int) ($config['retry_base_delay_ms'] ?? 1000))
                ->withTokenLeeway((int) ($config['token_leeway'] ?? 60));

            // Используем кэш Laravel для хранения токена между запросами.
            if (($config['use_cache'] ?? true) && $app->bound(CacheInterface::class)) {
                /** @var CacheInterface $cache */
                $cache = $app->make(CacheInterface::class);
                $configuration = $configuration->withCache($cache);
            }

            return $configuration;
        });

        $this->app->singleton(Client::class, static function (Container $app): Client {
            return new Client($app->make(Configuration::class));
        });

        // Псевдоним для фасада и удобного резолва.
        $this->app->alias(Client::class, 'avito-ads');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => $this->app->configPath('avito-ads.php'),
            ], 'avito-ads-config');
        }
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [Configuration::class, Client::class, 'avito-ads'];
    }

    private function configPath(): string
    {
        return __DIR__ . '/../../../config/avito-ads.php';
    }
}
