<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return static function (ContainerInterface $container): App {
    /**
     * @var App<ContainerInterface|null> $app
     */
    $app = AppFactory::createFromContainer($container);

    (require __DIR__ . '/../config/middleware.php')($app);
    (require __DIR__ . '/../config/routes.php')($app);

    return $app;
};
