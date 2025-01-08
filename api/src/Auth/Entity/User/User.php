<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use ArrayObject;
use DateTimeImmutable;
use DomainException;

final class User
{
    private ArrayObject $socialMedias;

    private function __construct(
        private readonly Id $id,
        private readonly DateTimeImmutable $date,
        private readonly Email $email,
        private readonly ?string $passwordHash = null,
        private ?Token $joinConfirmationToken = null,
        private Status $status = Status::Wait,
    ) {
        $this->socialMedias = new ArrayObject();
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

    public static function joinBySocialMedia(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        SocialMediaIdentity $identity
    ): self {
        $user = new self(
            id: $id,
            date: $date,
            email: $email,
            status: Status::Active,
        );

        $user->socialMedias->append($identity);

        return $user;
    }

    public function attachSocialMedia(SocialMediaIdentity $identity): void
    {
        /** @var SocialMediaIdentity $existing */
        foreach ($this->socialMedias as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new DomainException('Social media is already attached.');
            }
        }

        $this->socialMedias->append($identity);
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

    /** @return list<SocialMediaIdentity> */
    public function socialMedias(): array
    {
        /** @var list<SocialMediaIdentity> */
        return $this->socialMedias->getArrayCopy();
    }
}
