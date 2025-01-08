<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;

final class User
{
    public function __construct(
        private readonly Id $id,
        private readonly DateTimeImmutable $date,
        private readonly Email $email,
        private readonly string $passwordHash,
        private ?Token $joinConfirmToken = null,
        private Status $status = Status::Wait,
    ) {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function joinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }
    
    public function isActive(): bool
    {
        return $this->status->isActive();
    }
    
    public function isWait(): bool
    {
        return $this->status->isWait();
    }
}
