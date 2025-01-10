<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DomainException;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasBySocialMedia(SocialMediaIdentity $identity): bool;
    public function findByConfirmationToken(string $token): ?User;
    public function findByPasswordResetToken(string $token): ?User;
    public function findByNewEmailToken(string $token): ?User;
    public function add(User $user): void;
    public function remove(User $user): void;

    /** @throws DomainException */
    public function get(Id $id): User;

    /** @throws DomainException */
    public function getByEmail(Email $email): User;
}
