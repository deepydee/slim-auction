<?php

declare(strict_types=1);

use App\Http\Action\HomeAction;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;

http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

$builder = new DI\ContainerBuilder();

$builder->addDefinitions([
    'config' => [
        'debug' => (bool) getenv('APP_DEBUG'),
    ],
]);

$container = $builder->build();

$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware(
    displayErrorDetails: $container->get('config')['debug'],
    logErrors: true,
    logErrorDetails: true,
);

$app->get('/', HomeAction::class);

$app->run();