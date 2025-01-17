#!/usr/bin/env php
<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

require __DIR__ . '/../vendor/autoload.php';

if (getenv('SENTRY_DSN')) {
    \Sentry\init([
        'dsn' => getenv('SENTRY_DSN'),
        'traces_sample_rate' => 1.0,
        'profiles_sample_rate' => 1.0,
    ]);
}

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console');

if (getenv('SENTRY_DSN')) {
    $cli->setCatchExceptions(false);
}

/** @var array<string, array<string, mixed>> $config */
$config = $container->get('config');

/** @var list<class-string> $commands */
$commands = $config['console']['commands'];
foreach ($commands as $name) {
    /** @var Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();
