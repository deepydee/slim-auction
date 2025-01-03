<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
    displayErrorDetails: (bool) getenv('APP_DEBUG'),
    logErrors: true,
    logErrorDetails: true,
);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('{}');
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();