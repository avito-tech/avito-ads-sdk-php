<?php

/*
 * Проверка версии для CI: Client::VERSION == Client::API_VERSION == info.version в openapi.json.
 * При сборке по тегу (GITHUB_REF_TYPE=tag) тег тоже должен совпадать: v<версия>.
 * Расхождение — ненулевой код выхода (красная сборка).
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$spec = json_decode((string) file_get_contents(__DIR__ . '/../openapi.json'), true);
$specVersion = is_array($spec) && isset($spec['info']['version']) ? (string) $spec['info']['version'] : '';

$versions = [
    'Client::VERSION' => \AvitoAds\Client::VERSION,
    'Client::API_VERSION' => \AvitoAds\Client::API_VERSION,
    'openapi.json info.version' => $specVersion,
];

if (getenv('GITHUB_REF_TYPE') === 'tag') {
    $versions['git tag'] = ltrim((string) getenv('GITHUB_REF_NAME'), 'v');
}

foreach ($versions as $source => $version) {
    echo str_pad($source, 28), $version, PHP_EOL;
}

if (count(array_unique(array_values($versions))) !== 1 || $specVersion === '') {
    fwrite(STDERR, 'Версии расходятся: SDK, API_VERSION и openapi.json должны совпадать.' . PHP_EOL);
    exit(1);
}

echo 'OK: версия ', $specVersion, PHP_EOL;
