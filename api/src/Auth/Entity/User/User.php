<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use DomainException;

final class User
{
    private function __construct(
        private readonly Id $id,
        private readonly DateTimeImmutable $date,
        private readonly Email $email,
        private readonly ?string $passwordHash = null,
        private ?Token $joinConfirmationToken = null,
        private Status $status = Status::Wait,
    ) {
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        return new self(
            id: $id,
            date: $date,
            email: $email,
            passwordHash: $passwordHash,
            joinConfirmationToken: $token,
        );
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

    public function joinConfirmationToken(): ?Token
    {
        return $this->joinConfirmationToken;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (is_null($this->joinConfirmationToken)) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmationToken->validate($token, $date);
        $this->status = Status::Active;
        $this->joinConfirmationToken = null;
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
