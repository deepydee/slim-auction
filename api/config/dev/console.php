<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

return [
    DropCommand::class => static function (ContainerInterface $container): DropCommand {
        return new DropCommand($container->get(EntityManagerProvider::class));
    },
    CreateCommand::class => static function (ContainerInterface $container): CreateCommand {
        return new CreateCommand($container->get(EntityManagerProvider::class));
    },

    'config' => [
        'console' => [
            'commands' => [
                CreateCommand::class,
                DropCommand::class,
            ],
        ],
    ],
];
