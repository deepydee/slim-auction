<?php

declare(strict_types=1);

use App\Console;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;

return [
    EntityManagerProvider::class => static function (ContainerInterface $container): EntityManagerProvider {
        return new SingleManagerProvider($container->get(EntityManagerInterface::class));
    },
    ValidateSchemaCommand::class => static function (ContainerInterface $container): ValidateSchemaCommand {
        return new ValidateSchemaCommand($container->get(EntityManagerProvider::class));
    },

    'config' => [
        'console' => [
            'commands' => [
                Console\HelloCommand::class,
                ValidateSchemaCommand::class,
            ],
        ],
    ],
];
