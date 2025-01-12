<?php

declare(strict_types=1);

use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;

return [
    UserRepository::class => static function (ContainerInterface $container): UserRepository {
        $em = $container->get(EntityManagerInterface::class);
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(User::class);

        return new UserRepository($em, $repo);
    },
];
