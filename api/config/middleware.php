<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

return static function (App $app, ContainerInterface $container): void {
    $app->addErrorMiddleware(
        displayErrorDetails: $container->get('config')['debug'],
        logErrors: true,
        logErrorDetails: true,
    );
};
