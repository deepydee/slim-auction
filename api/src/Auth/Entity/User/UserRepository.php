<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final readonly class UserRepository
{
    /**
     * @param EntityRepository<User> $repo
     */
    public function __construct(private EntityManagerInterface $em, private EntityRepository $repo)
    {
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->value())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasBySocialMedia(SocialMedia $socialMedia): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->innerJoin('t.social_medias', 'n')
            ->andWhere('n.social_media.name = :name and n.social_media.identity = :identity')
            ->setParameter(':name', $socialMedia->name())
            ->setParameter(':identity', $socialMedia->identity())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByJoinConfirmationToken(string $token): ?User
    {
        return $this->repo->findOneBy(['joinConfirmationToken.value' => $token]);
    }

    public function findByPasswordResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['passwordResetToken.value' => $token]);
    }

    public function findByNewEmailToken(string $token): ?User
    {
        return $this->repo->findOneBy(['newEmailToken.value' => $token]);
    }

    public function get(Id $id): User
    {
        $user = $this->repo->find($id->value());
        if (is_null($user)) {
            throw new DomainException('User is not found.');
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        $user = $this->repo->findOneBy(['email' => $email->value()]);
        if (is_null($user)) {
            throw new DomainException('User is not found.');
        }

        return $user;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
