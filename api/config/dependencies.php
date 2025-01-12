<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$environment = getenv('APP_ENV');
if ($environment === false) {
    $environment = 'prod';
}

$aggregator = new ConfigAggregator([
    new PhpFileProvider(__DIR__ . '/common/*.php'),
    new PhpFileProvider(__DIR__ . '/' . $environment . '/*.php'),
]);

return $aggregator->getMergedConfig();
