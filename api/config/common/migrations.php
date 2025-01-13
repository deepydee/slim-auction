<?php

declare(strict_types=1);

use Doctrine\Migrations;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    DependencyFactory::class => static function (ContainerInterface $container) {
        $entityManager = $container->get(EntityManagerInterface::class);

        $configuration = new Configuration();
        $configuration->addMigrationsDirectory('App\Data\Migrations', __DIR__ . '/../../src/Data/Migrations');
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = $container->get(TableMetadataStorageConfiguration::class);
        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        return DependencyFactory::fromEntityManager(
            new Migrations\Configuration\Migration\ExistingConfiguration($configuration),
            new Migrations\Configuration\EntityManager\ExistingEntityManager($entityManager)
        );
    },
    TableMetadataStorageConfiguration::class =>  static function () {
        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName('migrations');

        return $storageConfiguration;
    },
    Command\ExecuteCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\ExecuteCommand($factory);
    },
    Command\MigrateCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\MigrateCommand($factory);
    },
    Command\LatestCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\LatestCommand($factory);
    },
    Command\ListCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\ListCommand($factory);
    },
    Command\StatusCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\StatusCommand($factory);
    },
    Command\UpToDateCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\UpToDateCommand($factory);
    },
    Command\DiffCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\DiffCommand($factory);
    },
    Command\GenerateCommand::class => static function (ContainerInterface $container) {
        $factory = $container->get(DependencyFactory::class);

        return new Command\GenerateCommand($factory);
    },
];
