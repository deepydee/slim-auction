<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

return static function (App $app, ContainerInterface $container): void {
    /** @var array{debug: bool} $config */
    $config = $container->get('config');

    $shouldDebug = $config['debug'];
    $app->addErrorMiddleware(
        displayErrorDetails: $shouldDebug,
        logErrors: true,
        logErrorDetails: true,
    );
};
