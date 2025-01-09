<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use ArrayObject;
use DateTimeImmutable;
use DomainException;

final class User
{
    private ArrayObject $socialMedias;
    private ?Token $passwordResetToken = null;

    private function __construct(
        private readonly Id $id,
        private readonly DateTimeImmutable $date,
        private readonly Email $email,
        private ?string $passwordHash = null,
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

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (! $this->isActive()) {
            throw new DomainException('User is not active.');
        }

        if (! is_null($this->passwordResetToken) && ! $this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }

        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if (is_null($this->passwordResetToken)) {
            throw new DomainException('Resetting is not requested.');
        }

        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (is_null($this->passwordHash)) {
            throw new DomainException('User does not have an old password.');
        }

        if (! $hasher->validate($current, $this->passwordHash)) {
            throw new DomainException('Incorrect current password.');
        }

        $this->passwordHash = $hasher->hash($new);
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

    public function passwordResetToken(): ?Token
    {
        return $this->passwordResetToken;
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
