<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
use Doctrine\Migrations;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{fixture_paths:list<string>} $config
         */
        $config = $container->get('config')['console'];

        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand(
            $em,
            $config['fixture_paths'],
        );
    },
    DropCommand::class => static function (ContainerInterface $container): DropCommand {
        return new DropCommand($container->get(EntityManagerProvider::class));
    },
    CreateCommand::class => static function (ContainerInterface $container): CreateCommand {
        return new CreateCommand($container->get(EntityManagerProvider::class));
    },

    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,
                CreateCommand::class,
                DropCommand::class,
                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ],
        ],
    ],
];
