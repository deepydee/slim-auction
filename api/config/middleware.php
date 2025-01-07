<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

return static function (App $app, ContainerInterface $container): void {
    /** @var array{debug: bool, env: string} $config */
    $config = $container->get('config');

    $app->addErrorMiddleware(
        displayErrorDetails: $config['debug'],
        logErrors: $config['env'] !== 'test',
        logErrorDetails: true,
    );
};
