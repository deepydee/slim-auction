<?php

declare(strict_types=1);

$environment = getenv('APP_ENV');
if ($environment === false) {
    $environment = 'prod';
}

$common = glob(__DIR__ . '/common/*.php');
if ($common === false) {
    $common = [];
}

$other = glob(__DIR__ . '/' . $environment . '/*.php');
if ($other === false) {
    $other = [];
}

$files = array_merge($common, $other);

/**
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress UnresolvableInclude
 * @psalm-suppress MixedInferredReturnType
 */
$configs = array_map(static fn (string $file): array => require $file, $files);

return array_replace_recursive(...$configs);
